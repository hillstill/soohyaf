<?php
namespace Prj;
class ManagerCtrl  extends \Prj\BaseCtrl {

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
		$this->onInit_chkLogin();
		//$render = $this->ini->viewRenderType();
		$this->initForUriDefault();
	}
	
	protected function tabname($act=null,$ctrl=null,$mod=null)
	{
		if($act===null){
			$act = $this->_request->getActionName();
		}
		if($ctrl===null){
			$ctrl = $this->_request->getControllerName();
		}
		if($mod===null){
			$mod = $this->_request->getModuleName();
		}
		$ret = $this->manager->acl->getMenuPath($act,$ctrl,$mod);
		if($ret){
			$tmp=explode('.',$ret);
			return 'page_'.array_pop($tmp);
		}else{
			throw new \ErrorException("unknown tabname for $mod/$ctrl/$act");
		}
	}
	
	protected function returnError($msg,$code=300)
	{
		$this->ini->viewRenderType('json');
		$this->_view->assign('statusCode',$code);
		if(!empty($msg)){
			$this->_view->assign('message',$msg);
		}
	}
	protected function returnOK($msg='',$code=200)
	{
		$this->ini->viewRenderType('json');
		$this->_view->assign('statusCode',$code);
		if(!empty($msg)){
			$this->_view->assign('message',$msg);
		}
	}
	/**
	 * 关闭当前页面或窗口，如果指定了$tabPageId，则刷新对应的tab页
	 * @param string $tabPageId
	 */
	protected function closeAndReloadPage($tabPageId=null)
	{
		$this->_view->assign ('callbackType', 'closeCurrent');
		if($tabPageId){
			$this->_view->assign ('navTabId', $tabPageId);
		}
	}
	
	protected function downExcel($records,$title=null,$filename=null)
	{
		if($filename===null){
			$filename = str_replace('page_', '', $this->tabname()).'_'.date('Y_m_d');
		}
		$this->ini->viewRenderType('echo');
		header("Pragma:public");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl;charset=gb2312");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
		header("Content-Transfer-Encoding:binary");
		reset($records);
		if(empty($title)){
			$title = array_keys(current($records));
		}
		echo iconv('utf-8', 'gbk', implode("\t", $title))."\n";
		foreach($records as $r){
			echo iconv('utf-8', 'gbk', implode("\t", $r))."\n";
		}
	}
	
	protected function getInputs()
	{
		return array_merge($this->_request->getQuery(),$this->_request->getPost(),$this->_request->getParams());
	}

	protected function getManagerLog($managerId,$pageid)
	{
		return array(
			array('managerId'=>'191704475514345770','evt'=>'login'),
			array('managerId'=>'502774624898912753','evt'=>'logout'),
		);
	}

	protected function onInit_chkLogin()
	{
		$this->session = \Sooh\Base\Session\Data::getInstance();
		if($this->session){
			$userId = $this->session->get('managerId');
			if ($userId){
				\Sooh\Base\Log\Data::getInstance()->userId = $userId;
				$this->manager = \Prj\Data\Manager::getCopyByManagerId($userId);
				$this->manager->load();
				if(!$this->manager->acl->hasRightsFor($this->_request->getModuleName(), $this->_request->getControllerName())){
					$this->returnError(\Prj\ErrCode::errNoRights,300);
				}
			}else{
				$this->returnError(\Prj\ErrCode::errNotLogin,301);
			}
		}
	}
	/**
	 *
	 * @var \Sooh\Base\Session\Data 
	 */
	protected $session=null;
	/**
	 *
	 * @var \Prj\Data\Manager
	 */
	protected $manager=null;
	protected function initForUriDefault($subdir = '/manage') {
		parent::initForUriDefault($subdir);
	}
}
