<?php
if(!defined('SOOH_INDEX_FILE')){
	define ('SOOH_INDEX_FILE', 'console.php');
}
parse_str($argv[1],$argv);
$route = explode('/',$argv['__']);
if(sizeof($route)==2){
	array_unshift($route, 'Index');
}
$argv['__VIEW__']='json';
$reqeustReal = new Yaf_Request_Simple("CLI", $route[0], $route[1], $route[2], $argv);

include __DIR__.'/../public/index.php';