<?php
/**
 * 执行模块的方法
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PassportController extends \Prj\UserCtrl {
	const tmp_clientType=127;
	/**
	 * 不用检查登入情况
	 */
	protected function onInit_chkLogin(){}
	public function loginAction()
	{
		$acc = $this->getAccount();
		try{
			$validImg=$this->_request->get('valid');
			$sessionData=\Sooh\Base\Session\Data::getInstance();
			error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>$validImg !=" . $sessionData->get('validImg'));
			if($validImg !== $sessionData->get('validImg')){
				return $this->returnError('invalidCode');
			}			
			$accountInfo = $acc->login($this->_request->get('loginname'), $this->_request->get('passwd'),'local',array('contractId','rights'));
			$this->onLogin($accountInfo);
			\Sooh\Base\Log\Data::getInstance()->ret = 'login ok';
		}  catch (\Exception $e){
			\Sooh\Base\Log\Data::getInstance()->ret = $e->getMessage();
			$this->returnError($e->getMessage(),$e->getCode());
		}
	}

	public function registerAction()
	{
		$acc = $this->getAccount();
		$loginname = $this->_request->get('loginname');
		$passwd=$this->_request->get('passwd');
		$contractId=$this->_request->get('contractId');
		$validImg=$this->_request->get('valid');
		$sessionData=\Sooh\Base\Session\Data::getInstance();
		if($validImg !== $sessionData->get('validImg')){
			return $this->returnError('invalidCode');
		}
		if(empty($contractId)){
			$contractId = $_COOKIE['contractId'];
		}
		if(empty($contractId)){
			$contractId = 0;
		}
		try{
			$accountInfo = $acc->register($loginname, $passwd,'local',array('contractId'=>$contractId,'regIP'=>  \Sooh\Base\Tools::remoteIP(),'clientType'=>self::tmp_clientType));
			$this->onLogin($accountInfo);
		}  catch (\Exception $e){
			$this->returnError($e->getMessage(),$e->getCode());
		}
	}
	protected function onLogin($accountInfo)
	{
		
		$sess = \Sooh\Base\Session\Data::getInstance();
		$sess->set('accountId', $accountInfo['accountId']);
		$sess->set('nickname', $accountInfo['nickname']);
		$this->_view->assign('account',array(
				'accountId'=>$accountInfo['accountId'],
				'nickname'=>$accountInfo['nickname'],
			));
		/**
		$userOrAccountId = $this->user;
		$checkinBook = \Lib\Services\CheckinBook::getInstance();
		 */
		$userOrAccountId = $accountInfo['accountId'];
		$checkinBook = \Lib\Services\CheckinBook::getInstance($this->getRpcDefault('CheckinBook'));
		$this->_view->assign('checkinBook',$checkinBook->doGetTodayStatus(1, $userOrAccountId)['data']);
		$this->_view->assign('shopPoints',array(
				'nleft'=>'todo',
				'history'=>array('todo'),
			));
		
		$user = \Prj\Data\User::getCopy($userOrAccountId);
		setcookie('nickname',$accountInfo['nickname'],0,'/',  \Sooh\Base\Ini::getInstance()->cookieDomain());
		$user->load();
		$dt = \Sooh\Base\Time::getInstance();
		if($user->exists()===false){
			$user->setField('nickname', $accountInfo['nickname']);
			$user->setField('contractId', $accountInfo['contractId']);
			$user->setField('regYmd', $dt->YmdFull);
			$user->setField('regHHiiss', $dt->his);
			$user->setField('regClient', self::tmp_clientType);
			$user->setField('regIP', \Sooh\Base\Tools::remoteIP());
			//$user->setField(self::fieldUser, array());
			$user->update();
		}else{
			$user->setField('nickname', $accountInfo['nickname']);
			$user->setField('lastDt', $dt->timestamp());
			$user->setField('lastIP', \Sooh\Base\Tools::remoteIP());
			$user->update();
		}
		
		$sess->shutdown();
		$this->returnOK();
	}
	/**
	 * @return \Lib\Services\Account
	 */
	protected function getAccount()
	{
		//$acc = \Lib\Services\Account::getInstance();
		$acc = \Lib\Services\Account::getInstance($this->getRpcDefault('Account'));
		return $acc;
	}
}
