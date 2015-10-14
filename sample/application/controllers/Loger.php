<?php
/**
 * 提供给外部系统记录日志
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class LogerController extends \Prj\ServiceCtrl{
	/**
	 * 尝试获取用户，获取不到不报错
	 */
	protected function onInit_chkLogin()
	{
		//不校验登入情况
	}
	/**
	 * 记录打开页面的情况
	 * 
	 * @input string page 页面名（标示）
	 * @input string httpReferer httpReferer
	 * @sample {api_uri}&page=intro.html&httpReferer=www.baidu.com
	 * @return {"code":200,"msg":""} 正常情况
	 */
	public function openpageAction()
	{
		$loger = \Sooh\Base\Log\Data::getInstance();
		$loger->evt='openpage';
		$loger->target = $this->_request->get('page');
		$loger->userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$loger->ext = $this->_request->get('httpReferer');
		$this->_view->assign('code','200');
		$this->_view->assign('msg','');
	}
}
