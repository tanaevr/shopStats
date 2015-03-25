<?php

$settings = array();

$tmp = array(
	'namespace' => array(
		'xtype' => 'textfield',
		'value' => 'minishop2',
		'area' => 'shopstats_main',
	),
	'colors' => array(
		'xtype' => 'textfield',
		'value' => "[{check: '#00a65a'},{orders: '#00c0ef'}]",
		'area' => 'shopstats_main',
	),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'shopstats_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
