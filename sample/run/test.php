<?php
define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
include dirname(__DIR__) .'/conf/globals.php';
//include dirname(__DIR__) .'/conf/autoload.php';
//e1dwnj8esd.database.chinacloudapi.cn
$GLOBALS['CONF']['dbConf']['mssql2']=array('host'=>'e1dwnj8esd.database.chinacloudapi.cn','user'=>'db-admin-test','pass'=>'assword01!','type'=>'sqlsrv',
					'dbEnums'=>array('default'=>'jym-biz','TestObj'=>'FE_Auth.dbo'));//根据模块选择的具体的数据库名

set_time_limit(0);
//$dbMssql=$db = Sooh\DB\Broker::getInstance('mssql2');
//$db = $dbMysql = Sooh\DB\Broker::getInstance('default');
//try{
//	$r = $db->getRecord('FE_User.dbo.Users', '*',array('Cellphone'=>'13032105140'));
//	var_dump($r);
//} catch (\Sooh\DB\Error $ex) {
//	echo \Sooh\DB\Broker::lastCmd()."\n";
//	echo $ex->getCode().' ### '.$ex->getMessage();
//}
//$where = $db->newWhereBuilder();
//$tmp = $db->newWhereBuilder();
//$tmp->init('AND');
//$tmp->append(array('a1'=>1,'a2'=>2));
//$where->init('OR');
//$where->append(null,$tmp);
//$where->append('c', 1);

try{
	$s = '{"ret":"errorOnUpdateSession - {\"xdebug_message\":\"<tr><th align=\'left\' bgcolor=\'#f57900\' colspan=\\\"5\\\"><span style=\'background-color: #cc0000; color: #fce94f; font-size: x-large;\'>( ! )<\\\/span> Sooh\\\\Base\\\\ErrException: update failed, verid changed? in E:\\\\wangning\\\\github\\\\sooh\\\\src\\\\Sooh\\\\DB\\\\Types\\\\Mysql.php on line <i>43<\\\/i><\\\/th><\\\/tr>\\n<tr><th align=\'left\' bgcolor=\'#e9b96e\' colspan=\'5\'>Call Stack<\\\/th><\\\/tr>\\n<tr><th align=\'center\' bgcolor=\'#eeeeec\'>#<\\\/th><th align=\'left\' bgcolor=\'#eeeeec\'>Time<\\\/th><th align=\'left\' bgcolor=\'#eeeeec\'>Memory<\\\/th><th align=\'left\' bgcolor=\'#eeeeec\'>Function<\\\/th><th align=\'left\' bgcolor=\'#eeeeec\'>Location<\\\/th><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>1<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0000<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>155152<\\\/td><td bgcolor=\'#eeeeec\'>{main}(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh_yaf\\\\sample\\\\public\\\\index.php\' bgcolor=\'#eeeeec\'>..\\\\index.php<b>:<\\\/b>0<\\\/td><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>2<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0130<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>565920<\\\/td><td bgcolor=\'#eeeeec\'><a href=\'http:\\\/\\\/www.php.net\\\/Yaf-Application.run\' target=\'_new\'>run<\\\/a>\\n(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh_yaf\\\\sample\\\\public\\\\index.php\' bgcolor=\'#eeeeec\'>..\\\\index.php<b>:<\\\/b>24<\\\/td><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>3<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0210<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>964744<\\\/td><td bgcolor=\'#eeeeec\'>ServiceController->callAction(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh_yaf\\\\sample\\\\public\\\\index.php\' bgcolor=\'#eeeeec\'>..\\\\index.php<b>:<\\\/b>24<\\\/td><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>4<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0210<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>964760<\\\/td><td bgcolor=\'#eeeeec\'>ServiceController->indexAction(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh_yaf\\\\sample\\\\application\\\\controllers\\\\Service.php\' bgcolor=\'#eeeeec\'>..\\\\Service.php<b>:<\\\/b>27<\\\/td><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>5<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0220<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>967120<\\\/td><td bgcolor=\'#eeeeec\'><a href=\'http:\\\/\\\/www.php.net\\\/function.call-user-func-array\' target=\'_new\'>call_user_func_array<\\\/a>\\n(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh_yaf\\\\sample\\\\application\\\\controllers\\\\Service.php\' bgcolor=\'#eeeeec\'>..\\\\Service.php<b>:<\\\/b>48<\\\/td><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>6<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0220<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>967408<\\\/td><td bgcolor=\'#eeeeec\'>Sooh\\\\Base\\\\Session\\\\Storage->update(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh_yaf\\\\sample\\\\application\\\\controllers\\\\Service.php\' bgcolor=\'#eeeeec\'>..\\\\Service.php<b>:<\\\/b>48<\\\/td><\\\/tr>\\n<tr><td bgcolor=\'#eeeeec\' align=\'center\'>7<\\\/td><td bgcolor=\'#eeeeec\' align=\'center\'>0.0220<\\\/td><td bgcolor=\'#eeeeec\' align=\'right\'>969776<\\\/td><td bgcolor=\'#eeeeec\'>Sooh\\\\DB\\\\Base\\\\KVObj->update(  )<\\\/td><td title=\'E:\\\\wangning\\\\github\\\\sooh\\\\src\\\\Sooh\\\\Base\\\\Session\\\\Storage.php\' bgcolor=\'#eeeeec\'>..\\\\Storage.php<b>:<\\\/b>76<\\\/td><\\\/tr>\\n\"}","logGuid":"00001777793829506944","ip":"127.0.0.1","ymd":"20150721","hhiiss":"134953","deviceId":"127.0.0.1","userId":6944,"isLogined":0,"opcount":0,"clientType":0,"contractId":"0","evt":"Index\/Service\/call","mainType":"","subType":"","target":"","num":0,"ext":"","narg1":0,"narg2":0,"narg3":0,"sarg1":"","sarg2":"","sarg3":"","resChanged":[]}';
	$r = json_decode($s,true);
	var_dump($r);
	
//$db->getRecords('temp', '*',$where,null,10,20);
}catch(\ErrorException $e){
	//echo \Sooh\DB\Broker::lastCmd();
}


//\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::duplicateKey);
//
//Sooh\DB\Broker::free();