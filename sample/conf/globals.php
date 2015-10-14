<?php
$GLOBALS['CONF']['path_php'] = 'php';
$GLOBALS['CONF']['path_console'] = APP_PATH.'/run/crond.php';
//$GLOBALS['CONF']['released'] = false;
$GLOBALS['CONF']['version'] = '1.0';
$GLOBALS['CONF']['deploymentCode'] = '10';//10:dev | 20:test | 30:pre | 40:online

$GLOBALS['CONF']['RpcConfig'] = array(
	'key'=>'asgdfw4872hfhjksdhr8732trsj','protocol'=>'HttpGet',
	'urls'=>array('http://127.0.0.1/soohsample/index.php?__=service/call',)
);
$GLOBALS['CONF']['maintainTime'] = array(mktime(23,59,59,1970,12,30)-60,mktime(23,59,59,1971,12,30),);

$GLOBALS['CONF']['dbConf']=array(
	'default'=>array('host'=>'127.0.0.1','user'=>'root','pass'=>'Aa111111','type'=>'mysql','port'=>'3306',
					'dbEnums'=>array('default'=>'db_b2b',)),//根据模块选择的具体的数据库名
	'serv1'=>array('host'=>'127.0.0.1','user'=>'root','pass'=>'Aa111111','type'=>'mysql','port'=>'3306',
					'dbEnums'=>array('default'=>'db_b2blog','session'=>'db_ram')),//根据模块选择的具体的数据库名	
	'serv2'=>array('host'=>'127.0.0.1','user'=>'root','pass'=>'Aa111111','type'=>'mysql','port'=>'3306',
					'dbEnums'=>array('default'=>'db_b2blog','session'=>'db_ram')),//根据模块选择的具体的数据库名
);


//$GLOBALS['CONF']['dbConf']=array(
//        'default'=>array('host'=>'192.168.56.110','user'=>'root','pass'=>'123456','type'=>'mysql','port'=>'3306',
//                                        'dbEnums'=>array('default'=>'db_b2b','monitor'=>'db_monitor')),//根据模块选择的具体的数据库名
//        'serv1'=>array('host'=>'192.168.56.120','user'=>'root','pass'=>'123456','type'=>'mysql','port'=>'3306',
//                                        'dbEnums'=>array('default'=>'db_accounts','dbgrpForLog'=>'db_b2blog')),//根据模块选择的具体的数据库名  
//		'serv2'=>array('host'=>'192.168.56.130','user'=>'root','pass'=>'123456','type'=>'mysql','port'=>'3306',
//                                        'dbEnums'=>array('default'=>'db_accounts','dbgrpForLog'=>'db_b2blog')),//根据模块选择的具体的数据库名  
//);

$GLOBALS['CONF']['dbByObj']=array(
		'default'=>array('default'),
		'dbgrpForLog'=>array('serv1','serv2'),
		'session'=>array('serv1','serv2'),
		'account'=>array('serv1','serv2'),
		//'monitor'=>array('default'),error_log,crond_log
	);

$GLOBALS['CONF']['localLibs']=array('Lib','Prj');

function var_log($var,$prefix=''){
	if(is_a($var, "\Exception")){
		$s = $var->__toString();
		if(strpos($s,'[Sooh_Base_Error]')){
			if(class_exists('\Sooh\DB\Broker',false)){
				$sql = "\n".\Sooh\DB\Broker::lastCmd()."\n";
			}else{
				$sql = "\n";
			}
			error_log(str_replace('[Sooh_Base_Error]',$sql,$s));
		}else{
			error_log($prefix.$var->getMessage()."\n".$s);
		}
	}else{
		error_log($prefix."\n".var_export($var,true));
	}
}

include "D:\\greensoft\\phpcomposer\\vendor\\autoload.php";