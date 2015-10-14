<?php
/**
 * 执行模块的方法
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ServiceController extends \Prj\ServiceCtrl {
	/**
	 * 记录日志的时候的 deviceid对应远程服务器 和 userid对应本地服务器
	 * @return array array(deviceid,userid)
	 */
	protected function initSession()
	{
		if('SessionStorage' == $this->_request->get('service')){
			\Lib\Services\SessionStorage::setStorageIni('session',2);
			error_log('session setted');
		}else{
			return array(
				$_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'], 
				$_SERVER['SERVER_NAME'].':'. getmypid()
			);
		}
	}
//	public function init()
//	{
//		//parent::init();
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'trace');
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'error');
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 2),'evt');
//		$log = \Sooh\Base\Log\Data::getInstance('c');
//		$log->clientType=0;
//		$log->deviceId = $_SERVER['REMOTE_ADDR'];
//		$log->contractId='0';
//		$log->evt =$this->_request->getModuleName().'/'.$this->_request->getControllerName().'/'.$this->_request->getActionName();
//		$log->isLogined=0;
//		$log->userId= $_SERVER['SERVER_NAME'].':'. getmypid();
//
//		\Lib\Services\SessionStorage::setStorageIni();
//	}
	
	public function callAction()
	{
		$this->indexAction();
	}
	public function indexAction()
	{
		\Sooh\Base\Ini::getInstance()->viewRenderType('json');
		$class = $this->_request->get('service');
		$method = $this->_request->get('cmd');
		$class = explode('\\', $class);
		$class = ucfirst(array_pop($class));
		$logData = \Sooh\Base\Log\Data::getInstance();
		$logData->mainType = $class;
		$logData->subType = $method;
		$logData->target= "$class/$method";
		$logData->ret = "service:$class/$method";
if('rpcservices'!=$class)error_log("RPC_called:$class/$method from {$_SERVER['REMOTE_ADDR']}");
		$appKey=null;
		if($class==ucfirst(\Sooh\Base\Rpc\Broker::serviceNameForRpcManager)){
			$appKey = \Sooh\Base\Ini::getInstance()->get('RpcConfig.key');
			if(empty($appKey)){
				return $this->error('service-key-missing ',503);
			}
		}
		if($this->checkSign($class,$appKey)==false){
			return;
		}
		$args =  json_decode($this->_request->get('args'),true);
		if (file_exists(__DIR__.'/../library/Lib/Services/'.$class.'.php')){
			$classname = '\\Lib\\Services\\'.$class;
			if(method_exists($classname, $method)){
				try{
					$obj = $classname::getInstance();
					$ret = call_user_func_array(array($obj,$method), $args);
					$this->_view->assign('code',200);
					$this->_view->assign('data',$ret);
					//$this->_view->assign('lastsql', \Sooh\DB\Broker::lastCmd());
					//$this->_view->assign('lastsql', base64_encode(\Sooh\DB\Broker::lastCmd()));
					\Sooh\Base\Log\Data::getInstance()->ret = "done";
				}  catch (\Exception $e){
					if(!empty($e->customData)){
						$this->_view->assign('data',$e->customData);
					}
					$this->error($e->getMessage(),$e->getCode());
				}
			}else{
				$this->error('service-cmd error:'.$method.' of '.$classname);
			}
		}else{
			$this->error('service-name error:'.$class);
		}
	}
	protected function error($msg,$code=400)
	{
		\Sooh\Base\Log\Data::getInstance()->ret = $msg;
		$this->_view->assign('code',$code);
		$this->_view->assign('msg',$msg);
	}
	protected function checkSign($service,$appKey=null)
	{
		$dt = $this->_request->get('dt');
		$sign = $this->_request->get('sign');

		
		if($appKey===null){
			$arr = \Sooh\Base\Rpc\Broker::getRpcIni($service);
			$appKey= $arr['key'];
		}
		if(\Sooh\Base\Ini::getInstance()->get('released')){
			$dur = abs($dt-time());
			if($dur>60){
				$this->error('sign error');
				return false;
			}
			if($sign != md5($dt.$appKey)){
				$this->error('sign error');
				return false;
			}
		}else{
			if(empty($sign)){
				error_log('trace:sign of serviceCtrl skipped');
			}elseif($sign != md5($dt.$appKey)){
				$this->error('sign error');
				return false;
			}
		}
		return true;
	}
}
