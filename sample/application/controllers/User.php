<?php
/**
 * 用户基本类，包括改密码
 */
class UserController extends \Prj\UserCtrl
{
	public function infoAction()
	{
		$this->user->load();
		$this->_view->assign('code',200);
		$this->_view->assign('UserInfo',array(
										'name'=>$this->user->getField('nickname'),
										'lastIP'=>  $this->user->getField('lastIP'),
										'lastDt'=>date('Y-m-d H:i:s',$this->user->getField('lastDt'))
							));
	}
}
