<?php
/**
 * 管理员基本类，主要处理登入，登出
 * 
 */

class ManagerController extends \Prj\ManagerCtrl
{

	/**
	 * 改造：没登入的情况下不丢异常
	 */
	protected function onInit_chkLogin()
	{
		$this->session = \Sooh\Base\Session\Data::getInstance();
		if($this->session){
			$userId = $this->session->get('managerId');
			if (!empty($userId)){
				\Sooh\Base\Log\Data::getInstance()->userId = $userId;
				$this->manager = \Prj\Data\Manager::getCopyByManagerId($userId);
			}else{
				$this->manager = null;
			}
		}
	}
	
	public function indexAction()
	{
		if($this->_request->get('dowhat')=='logout'){
			$this->_view->assign('useTpl','logout');
			\Sooh\Base\Session\Data::getInstance()->set('managerId', '');
		}elseif($this->manager){//已经登入,进主页
			$this->manager->load();
			$this->_view->assign('leftmenus',$this->manager->acl->getMenuMine());
			$this->_view->assign('useTpl','homepage');
		}else{//尚未登入，去登入页
			$u = $this->_request->get('u');
			$p = $this->_request->get('p');
			if(!empty($u) && !empty($p)){
				$this->ini->viewRenderType('json');
				$acc = \Lib\Services\Manager::getInstance(null);
				try{
					$validImg=$this->_request->get('valid');
					$sessionData=\Sooh\Base\Session\Data::getInstance();
					if($validImg !== $sessionData->get('validImg')){
						return $this->returnError('invalidCode');
					}			
					$accountInfo = $acc->login($u, $p,'local',array('rights'));
					$log = \Sooh\Base\Log\Data::getInstance();
					$log->ret = 'login ok';
					$log->ext = $accountInfo['nickname'];
					$sessionData->set('managerId', $accountInfo['accountId']);
					$sessionData->set('nickname', $accountInfo['nickname']);
					$this->returnOK();
				}  catch (\Exception $e){
					\Sooh\Base\Log\Data::getInstance()->ret = $e->getMessage();
					$this->returnError($e->getMessage());
				}
			}else{
				$acc = \Lib\Services\Manager::getInstance(null);
				$acc->addFirstAccount();
				$this->_view->assign('useTpl','login');
			}
		}
	}
	public function welcomeAction()
	{
		
	}

	public function resetpwdAction()
	{
		
	}


	protected function _frame()
	{
		$acl = \Sooh\DB\Acl\Acl::getInstance();
		if($this->_request->get('__LOGOUT__')==1){//登出
			$this->_view->assign('useTpl','logout');
			$acl->logout();
			return;
		}
		$isLogined=$acl->isLogined();
		$username = $this->_request->get('u');
		$password = $this->_request->get('p');
		//$returnUrl = $this->_request->get('returnUrl',$_SESSION['returnUrl']);
		//if(empty($returnUrl))	$returnUrl=\Sooh\Base\Tools::uri();
		$this->_view->assign ('returnUrl', \Sooh\Base\Tools::uri());
		if(!empty($username) && !empty($password)){
			try{
				$camefrom = $this->_request->get('camefrom','Jym');
				$acl->login($username, $password,3600,$camefrom);
				$account = $acl->getAclManager()->getAccount($username,$camefrom);
				$acl->setSessionVal('nickname', $account['nickname']);

//				if($this->_request->get('__ONLY__')!=='body'){
//					$this->_view->assign ('statusCode', '200');
////					$this->_view->assign ('callbackType', 'forward');
//					return;
//				}else{
					$this->_view->assign ('statusCode', '200');
//					return;
//				}
			}catch(\ErrorException $e){
				$this->_view->assign ('statusCode', '300');
				$this->_view->assign ('message', '登入失败：'.$e->getMessage());
//return;
			}
		}else{
			if($isLogined){
				$menu = $acl->menu();
				$this->_view->assign('menuleft',$menu);
				$this->_view->assign('useTpl','frame');
			}else{
				$this->_view->assign('useTpl',$this->_request->get('__ONLY__')==='body'?'login_withouthead':'login_withhead');
			}
		}
	}
}
