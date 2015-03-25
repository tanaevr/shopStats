<?php

/**
 * The base class for shopStats.
 */
class shopStats {
	/* @var modX $modx */
	public $modx;
	/** @var array $initialized */
	public $initialized = array();

	public $assetsLoaded = false;

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('shopstats_core_path', $config, $this->modx->getOption('core_path') . 'components/shopstats/');
		$assetsUrl = $this->modx->getOption('shopstats_assets_url', $config, $this->modx->getOption('assets_url') . 'components/shopstats/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'ctx' => 'mgr',
			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',
			'customPath' => $corePath.'model/custom/',
		), $config);

		$this->modx->addPackage('shopstats', $this->config['modelPath']);
		$this->modx->lexicon->load('shopstats:default');
	}

	// public function initialize($ctx = 'web', $scriptProperties = array()) {
	// 	$this->config = array_merge($this->config, $scriptProperties);
	// 	$this->config['ctx'] = $ctx;
	// 	if (!empty($this->initialized[$ctx])) {
	// 		return true;
	// 	}
	// 	switch ($ctx) {
	// 		case 'mgr':
	// 			//if (!$this->assetsLoaded) {
	// 				// $this->modx->regClientCSS('/assets/components/shopstats/css/mgr/AdminLTE.css');
	// 	 		//     $this->modx->regClientCSS('/assets/components/shopstats/css/mgr/morris/morris.css');
	// 				// $this->modx->regClientCSS('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
	// 				// $this->modx->regClientCSS('//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');

	// 				// $this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js\"></script>", true);
	// 				// $this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/js/mgr/bootstrap.min.js\"></script>", true);
	// 				// $this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js\"></script>", true);
	// 				// $this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/js/mgr/morris.min.js\"></script>", true);			
	// 	       // }
	// 	        //$this->assetsLoaded = true;
	// 		break;
	// 		default: break;
	// 	}

	// }

	public function getStats(){
		$stats = 'ok	';

		$this->modx->regClientCSS('/assets/components/shopstats/lumino/css/bootstrap.css');
		$this->modx->regClientCSS('/assets/components/shopstats/lumino/css/datepicker3.css');
		$this->modx->regClientCSS('/assets/components/shopstats/lumino/css/styles.css');

		$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/jquery-1.11.1.min.js\"></script>", true);
		$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/bootstrap.min.js\"></script>", true);
		$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/chart.min.js\"></script>", true);
		//$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/chart-data.js\"></script>", true);
		$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/easypiechart.js\"></script>", true);
		$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/easypiechart-data.js\"></script>", true);
		$this->modx->regClientStartupScript("<script type=\"text/javascript\" src=\"/assets/components/shopstats/lumino/js/bootstrap-datepicker.js\"></script>", true);

		require_once $this->config['modelPath'].'/shopstats/minishop2.class.php';
		$stats_class = $this->modx->getOption('shopstats_namespace', null, 'minishop2_shop');
		if ($stats_class != 'minishop2_shop') {$this->loadCustomClasses($stats_class);}


		$this->shop = new $stats_class($this, $this->config);
		if (!($this->shop instanceof statsInterface) || $this->shop->initialize($ctx) !== true) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize shop class: "'.$stats_class.'"');
			return false;
		}

		//$stats = $this->modx->toJSON($this->shop->getStats());
		$stats = $this->shop->getStats();

		foreach($stats['total_counts'] as $status_key => $status){
			foreach($stats['stats_month'] as $month_key => $month){
		        $labels[$month_key] = '"'.$month_key.'"';
		        if(count($month[$status_key]) > 0){
		            $dataCount[$status_key][$month_key] = $month[$status_key]['count_orders'];
		            //$dataCost[$status_key][$month_key] = $month[$status_key]['order_cost'];
		        }else{
		            $dataCount[$status_key][$month_key] = 0;
		            //$dataCost[$status_key][$month_key] = 0;
		        }

		        $dataCost[$status_key][$month_key] = !empty($month[$status_key]['total_cost']) ? $month[$status_key]['total_cost'] : 0;
		    }
		    $datasetsCount[] = '{
				label: "'.$status['name'].'",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#'.$status['color'].'",
				pointColor : "#'.$status['color'].'",
				pointStrokeColor : "#'.$status['color'].'",
				pointHighlightFill : "#'.$status['color'].'",
				pointHighlightStroke : "#'.$status['color'].'",
				data : ['.implode(",", $dataCount[$status_key]).']
			}';
			$datasetsCost[] = '{
				label: "'.$status['name'].'",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#'.$status['color'].'",
				pointColor : "#'.$status['color'].'",
				pointStrokeColor : "#'.$status['color'].'",
				pointHighlightFill : "#'.$status['color'].'",
				pointHighlightStroke : "#'.$status['color'].'",
				data : ['.implode(",", $dataCost[$status_key]).']
			}';
		}
		$datasetsCount = implode(",", $datasetsCount);
		$datasetsCost = implode(",", $datasetsCost);
		$labels = implode(",", $labels);

		$this->modx->regClientStartupScript('<script type="text/javascript">
			var lineChartCount = {
				labels: ['.$labels.'],
				datasets : [
					'.$datasetsCount.'
				]
			}
			var lineChartCost = {
				labels: ['.$labels.'],
				datasets : [
					'.$datasetsCost.'
				]
			}

			window.onload = function(){
				var chart1 = document.getElementById("line-chart").getContext("2d");
				window.myLine = new Chart(chart1).Line(lineChartCount, {
					responsive: true
				});
				var chart2 = document.getElementById("line-chart-cost").getContext("2d");
				window.myLine = new Chart(chart2).Line(lineChartCost, {
					responsive: true
				});

				var dashboard = $(".js-dashboard-stats");
				var d_height = dashboard.height();
				var d_parent = dashboard.parents(".dashboard-block");
				d_parent.addClass("dashboard-stats");
				d_parent.find("h3").hide();
				d_parent.find(".body").css("max-height", d_height+50);
				d_parent.height(d_height);


			};
		</script>', true);


		return $stats;
	}

	public function loadCustomClasses($dir) {
		$files = scandir($this->config['customPath'] . $dir);
		foreach ($files as $file) {
			if (preg_match('/.*?\.class\.php$/i', $file)) {
				include_once($this->config['customPath'] . $dir . '/' . $file);
			}
		}
	}

	function month($month){
		$months = array(
			'1' => 'Январь',
			'2' => 'Февраль',
			'3' => 'Март',
			'4' => 'Апрель',
			'5' => 'Май',
			'6' => 'Июнь',
			'7' => 'Июль',
			'8' => 'Август',
			'9' => 'Сентябрь',
			'10' => 'Октябрь',
			'11' => 'Ноябрь',
			'12' => 'Декабрь',
		);
		return $months[$month];
	}

}
