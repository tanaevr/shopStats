<?php
if (!$shopstats = $modx->getService('shopstats', 'shopStats', $modx->getOption('shopstats_core_path', null, $modx->getOption('core_path') . 'components/shopstats/') . 'model/shopstats/', array())) {
		return 'Could not load reditor class! plugin: shopStats';
	}
$chunkArr = $shopstats->getStats();

$date = date_parse(date('c'));
//print_r($chunkArr['stats_month'][$date['year'].'-'.$date['month']]);

foreach($chunkArr['total_counts'] as $status_key => $status){
	$statuses .= '<span style="background: #'.$status['color'].'"></span> '.$status['name'].'&nbsp;&nbsp;&nbsp;'; 
}
$modx->lexicon->load('shopstats:default');
$_lang =  $modx->lexicon->fetch();
$tpl = <<<EOT

	<div class="js-dashboard-stats">
		<div class="row">
			<div class="col-xs-12 col-md-4 col-lg-4">
				<div class="panel panel-blue panel-widget ">
					<div class="row no-padding">
						<div class="col-sm-3 col-lg-5 widget-left">
							<span class="glyphicon glyphicon-shopping-cart glyphicon-l"></span>
						</div>
						<div class="col-sm-9 col-lg-7 widget-right">
							<div class="large"><a href="/manager/?a=mgr/orders&namespace=minishop2">$chunkArr[cart_count]</a></div>
							<div class="text-muted">{$_lang['shopstats.total_orders']}</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-lg-4">
				<div class="panel panel-orange panel-widget">
					<div class="row no-padding">
						<div class="col-sm-3 col-lg-5 widget-left">
							<span class="glyphicon glyphicon-stats glyphicon-l"></span>
						</div>
						<div class="col-sm-9 col-lg-7 widget-right">
							<div class="large">{$_lang['shopstats.currency']} $chunkArr[cart_cost]</div>
							<div class="text-muted">{$_lang['shopstats.money_turnover']}</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-lg-4">
				<div class="panel panel-teal panel-widget">
					<div class="row no-padding">
						<div class="col-sm-3 col-lg-5 widget-left">
							<span class="glyphicon glyphicon-user glyphicon-l"></span>
						</div>
						<div class="col-sm-9 col-lg-7 widget-right">
							<div class="large">$chunkArr[users_count]</div>
							<div class="text-muted">{$_lang['shopstats.customers']}</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">{$_lang['shopstats.orders']}</div>
					<div class="panel-body">
						<div class="canvas-wrapper">
							<canvas class="main-chart" id="line-chart" height="200" width="600"></canvas>
						</div>
						<div class="easypiechart-panel">
						$statuses
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">{$_lang['shopstats.finance']}</div>
					<div class="panel-body">
						<div class="canvas-wrapper">
							<canvas class="main-chart" id="line-chart-cost" height="200" width="600"></canvas>
						</div>
						<div class="easypiechart-panel">
						$statuses
						</div>
					</div>
				</div>
			</div>
		</div><!--/.row-->
	</div>

EOT;

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array('name'=>"INLINE-".uniqid(),'snippet'=>$tpl));
$chunk->setCacheable(false);

$output = $chunk->process($chunkArr);

return $output;