<?php
if(!defined('SOOH_INDEX_FILE')){
	define ('SOOH_INDEX_FILE', 'crond.php');
}
parse_str($argv[1],$argv);
if(empty($argv)){
	$act = 'hourly';
}else{
	$act = 'run';
}
$argv['__VIEW__']='json';
$reqeustReal = new Yaf_Request_Simple("CLI", 'Index', 'crond', $act, $argv);

include __DIR__.'/../public/index.php';