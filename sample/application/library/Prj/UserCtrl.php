<?php
namespace Prj;
class UserCtrl  extends \Prj\BaseCtrl {

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
		parent::init();
		$render = $this->ini->viewRenderType();
		if($render==='html'){
			$this->ini->viewRenderType('json');
		}
		$this->onInit_chkLogin();
	}
	protected function onInit_chkLogin()
	{
		$userIdentifier = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		if ($userIdentifier){
			\Sooh\Base\Log\Data::getInstance()->userId = $userIdentifier;
			$this->user = \Prj\Data\User::getCopy($userIdentifier);
		}else{
			throw new \ErrorException(\Prj\ErrCode::errNotLogin,401);
		}
	}
	/**
	 *
	 * @var \Prj\Data\User;
	 */
	protected $user=null;

}
