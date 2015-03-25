<?php
interface statsInterface {
	public function initialize($ctx = 'mgr');

	function getStats();
}


class minishop2_shop implements statsInterface {
	/** @var modX $modx */ 
	public $modx;
	public $stats;

	function __construct(shopStats & $stats, array $config = array()) {
		$this->stats = & $stats;
		$this->modx = & $stats->modx;

		$this->modx->lexicon->load('minishop2:order');
	}

	public function initialize($ctx = 'mgr') {
		return true;
	}
	

	function getStats(){
		$output = '';

		$q_status = $this->modx->newQuery('msOrderStatus', array('active' => 1));
		$q_status->select('id,name,color');
		if ($q_status->prepare() && $q_status->stmt->execute()) {
			while ($row = $q_status->stmt->fetch(PDO::FETCH_ASSOC)) {
				//$output[$row['id']] = $row;
				$output['total_counts'][$row['id']] = array(
					'name' => $row['name'],
					'color' => $row['color'],
					'count_orders' => $this->modx->getCount('msOrder',array('status' => $row['id'])),
					);
			}
		}


		$q_stats_month = $this->modx->newQuery('msOrder');
		$q_stats_month->select('status,`createdon`, month(`createdon`) AS `order_month`, count(*) AS `order_count`, SUM(cart_cost) AS order_cost');
		$q_stats_month->groupby('month(`createdon`), status');
		$q_stats_month->sortby('createdon', ASC);

		if ($q_stats_month->prepare() && $q_stats_month->stmt->execute()) {
			$output['cart_cost'] = 0;
			$output['cart_count'] = 0;
			while ($row = $q_stats_month->stmt->fetch(PDO::FETCH_ASSOC)){
		    	$date = date_parse($row['createdon']);
		    	$output['stats_month'][$date['year'].'-'.$date['month']][$row['status']] = array(
		    		//'date' => $date['year'].'-'.$date['month'],
		    		'total_cost' => $row['order_cost'],
		    		'count_orders' => $row['order_count'],
		    		'status' => $row['status'],
		    		);
		    	$output['cart_cost'] += $row['order_cost'];
		    	$output['cart_count'] += $row['order_count'];
		    }
		    $output['cart_cost'] = number_format($output['cart_cost'], 2, ',', ' ');
		    $output['users_count'] = $this->modx->getCount('modUser',array('active' => 1, 'primary_group' => 0));
		}

		return $output;
	}
}