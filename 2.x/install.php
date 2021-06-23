<?php
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF')){
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc;

if(!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$column_array = array(
	'column1' => array(
		'name' => 'facebook', 
		'type' => 'varchar',
		'size' => '255',
		'null' => false,
		'default' => '',
	),
	'column2' => array(
		'name' => 'twitter', 
		'type' => 'varchar',
		'size' => '255',
		'null' => false,
		'default' => '',
	),
	'column3' => array(
		'name' => 'youtube', 
		'type' => 'varchar',
		'size' => '255',
		'null' => false,
		'default' => '',
	),
	'column4' => array(
		'name' => 'twitch', 
		'type' => 'varchar',
		'size' => '255',
		'null' => false,
		'default' => ''
	),
	'column5' => array(
		'name' => 'steam', 
		'type' => 'varchar',
		'size' => '255',
		'null' => false,
		'default' => ''
	)
);

foreach ($column_array as $key => $data)
{
	$smcFunc['db_add_column'](
		'{db_prefix}members',
		$data,
		array(),
		'update',
		'fatal'
	);
}

global $smcFunc;

if(!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$column_array = array(
	'column1' => array(
		'name' => 'customsmiicon', 
		'type' => 'varchar',
		'size' => '255',
		'null' => false,
		'default' => '',
	),
);

foreach ($column_array as $key => $data)
{
	$smcFunc['db_add_column'](
		'{db_prefix}custom_fields',
		$data,
		array(),
		'update',
		'fatal'
	);
}

$hooks = array(
	'integrate_pre_include' => '$sourcedir/Streamers.php',
	'integrate_menu_buttons' => 'streamers_menu',
);
foreach ($hooks as $hook => $function)
	add_integration_function($hook, $function);

?>