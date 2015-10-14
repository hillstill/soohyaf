<?php
namespace Prj;
class BaseCtrl  extends \Yaf_Controller_Abstract {

//	protected function getFromRaw()
//	{
//		$s = file_get_contents('php://input');
//		if(!empty($s)){
//			parse_str($s,$inputs);
//			return $inputs;
//		}else{
//			return $inputs=array();
//		}
//	}
	public function init()
	{
		$this->ini = \Sooh\Base\Ini::getInstance();
		if($this->_request->isPost()){
			$this->onInit_chkMaintainTime();
		}

		list($deviceId,$uid)=$this->initSession();
		
		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'trace');
		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'error');
		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 2),'evt');
		$log = \Sooh\Base\Log\Data::getInstance('c');
		$log->clientType=0;
		$log->deviceId = $deviceId;
		$log->contractId='0';
		$log->evt =$this->_request->getModuleName().'/'.$this->_request->getControllerName().'/'.$this->_request->getActionName();
		$log->isLogined=0;
		$log->userId=$uid;
	}
	protected function returnError($msg,$code=400)
	{
		$this->_view->assign('code',$code);
		if(!empty($msg)){
			$this->_view->assign('msg',$msg);
		}
	}
	protected function returnOK($msg='',$code=200)
	{
		$this->_view->assign('code',$code);
		if(!empty($msg)){
			$this->_view->assign('msg',$msg);
		}
	}
	/**
	 * 验证码的action 通用化
	 */
	public function validimgAction()
	{
		//php composer.phar require "gregwar/captcha"
		$builder = new \Gregwar\Captcha\CaptchaBuilder;
		$builder->build(90,26);
		header('Content-type: image/jpeg');
		$this->ini->viewRenderType('echo');
		$builder->output();
		\Sooh\Base\Session\Data::getInstance()->set('validImg', $builder->getPhrase());
	}
	/**
	 * @return array array(deviceid,userid)
	 */
	protected function initSession()
	{
		error_log('initSession called');
		$rpc = $this->getRpcDefault('SessionStorage');
		if($rpc==null){
			error_log('sessionStorage direct');
			\Lib\Services\SessionStorage::setStorageIni('session',2);
		}else{
			error_log('sessionStorage by rpc');
		}
		\Sooh\Base\Session\Data::getInstance( \Lib\Services\SessionStorage::getInstance($rpc));
		
		$deviceId = \Sooh\Base\Session\Data::getSessId();
		$uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		return array($deviceId,$uid);
	}
	
	protected function getRpcDefault($serviceName)
	{
		$flg = $this->ini->get('RpcConfig.force');
		if($flg!==null){
			if($flg){
				error_log('force rpc for '.$serviceName);
				return \Sooh\Base\Rpc\Broker::factory($serviceName);
			}else{
				error_log('no rpc for '.$serviceName);
				return null;
			}
		}else{
			error_log('try rpc for '.$serviceName);
			return \Sooh\Base\Rpc\Broker::factory($serviceName);
		}
	}

	/**
	 * 检查是否维护时间，如果是，抛出异常（不支持任何写动作）
	 * @throw \ErrorException
	 */
	protected function onInit_chkMaintainTime()
	{
		$now = \Sooh\Base\Time::getInstance()->timestamp();
		$chk = $this->ini->get('maintainTime');
		if ( $chk[1]> $now && $chk[0]<= $now){
			throw new \ErrorException(\Prj\ErrCode::errMaintainTime);
		}
	}
	protected function initForUriDefault($subdir='')
	{
		$request = $this->_request;
		$ini = $this->ini;
		
		$ini->initGobal(array('request'=>array('action'=>$request->getActionName(),
												'controller'=>lcfirst($request->getControllerName()),
												'module'=>lcfirst($request->getModuleName()),
												'baseUri'=>$subdir
												)
				));
	}
	/**
	 *
	 * @var \Sooh\Base\Ini 
	 */
	protected $ini;
}
