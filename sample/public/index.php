<?php
//php.ini 设置 yaf.use_spl_autoload=on
define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
if(!defined('SOOH_INDEX_FILE')){
	define ('SOOH_INDEX_FILE', 'index.php');
}
define('SOOH_ROUTE_VAR','__');
error_log("-------------------------------------------------------tart:route=".$_GET['__']." cmd=".$_GET['cmd']." pid=".  getmypid());
include dirname(__DIR__) .'/conf/globals.php';

$ini = \Sooh\Base\Ini::getInstance();
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");

$dispatcher = $app->getDispatcher();
if(!empty($reqeustReal)){
	$dispatcher->setRequest( $reqeustReal );
}

$view = \SoohYaf\SoohPlugin::initYafBySooh($dispatcher);
$dispatcher->returnResponse(TRUE);
try{
	$response = $app->run();
}catch(\ErrorException $e){
	$view->assign('code',$e->getCode());
	$view->assign('msg',$e->getMessage());
	error_log("Error Caught at index.php:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString()."\n");
	$response=new Yaf_Response_Http();
	$response->setBody($view->render('ctrl/action.phtml'));
}

if($ini->viewRenderType()==='json'){
	header('Content-type: application/json');
}
$response->response();

\Sooh\Base\Ini::registerShutdown(null, null);
error_log("====================================================================end:route=".$_GET['__']." cmd=".$_GET['cmd']." pid=".  getmypid());
