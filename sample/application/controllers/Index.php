<?php
/**
 * Description of Forward
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class IndexController extends \Prj\BaseCtrl {
	// https://addons.test.ad.jinyinmao.com.cn/V1/forward/voucherinterest?ymd=20150501&clienttype=900&settleDate=2015-6-10&yield=5.5
	// https://addons.test.ad.jinyinmao.com.cn/V1/forward/investing?ProductNo=YBC10001004&Count=10&BankCardNo=6222021077338790061&PaymentPassword=yj718603&ClientType=900&PrincipalVolumeId=51
	public function init()
	{
		$this->ini = \Sooh\Base\Ini::getInstance();
	}
	public function logAction()
	{
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'trace');
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'error');
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 2),'evt');
//		$l = \Sooh\Base\Log\Data::getInstance('c');
//		$l->clientType=900;
//		$l->deviceId = \Lib\Session::getSessId();
//		$l->appendResChange('gold', 1, 1);
//		$l->appendResChange('silver', 10, 20);
//		\Sooh\Base\Log\Data::onShutdown();
	}
	
	public function indexAction()
	{
		\Lib\Services\SessionStorage::setStorageIni();
		\Sooh\Base\Session\Data::getInstance( \Lib\Services\SessionStorage::getInstance($this->getRpcDefault('SessionStorage')));
		\Sooh\Base\Ini::getInstance()->viewRenderType('json');
		$this->_view->assign('normal',true);
		$this->_view->assign('sessionId', \Sooh\Base\Session\Data::getSessId());
		$this->_view->assign('accountId', \Sooh\Base\Session\Data::getInstance()->get('accountId'));
		$today = \Sooh\Base\Time::getInstance()->YmdFull;
		$uri0 = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].''.$_SERVER["SCRIPT_NAME"];
		$this->_view->assign('urls',  array(
			'login'=>$uri0.'?__=passport/login&loginname=qq01&passwd=123456',
			'register'=>$uri0.'?__=passport/register&loginname=qq01&passwd=123456&contractId=34562342523534',
			'checkin-checkin'=>$uri0.'?__=checkin/checkin&with_bonus=1',
			'checkin-today'=>$uri0.'?__=checkin/today&with_bonus=1',
			'checkin-reset'=>$uri0.'?__=checkin/resetday&ymd='.$today,
			'checkin-resetAll'=>$uri0.'?__=checkin/resetday&ymd='.$today.'&num=0',
		));
		//$this->_view->assign('code1',\Lib\Subdir\Abc::run());
		throw new \ErrorException('msg append by exception');
		//$this->_view->assign('code2',  \Subdir\Test::run());
		//$this->_view->assign('code3',\Subdir_Type1::run());
	}
	
	
}
