<?php
use Sooh\Base\Form\Item as form_def;
/**
 * 管理员一览
 * 
 */
class ManagersController extends \Prj\ManagerCtrl
{
	public function indexAction()
	{
		if(!$this->manager){
			return;
		}
		
		$pageid = $this->_request->get('pageid',1)-0;
		$pager = new \Sooh\DB\Pager($this->_request->get('pagesize',10),array(10,30,100),false);
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		//$frm->addItem('_camefrom_eq', form_def::factory('', 'local', form_def::constval));
		$frm->addItem('_loginname_lk', form_def::factory('帐号关键词', '', form_def::text))
			->addItem('_nickname_lk', form_def::factory('昵称关键词', '', form_def::text))
			->addItem('_dtForbidden_eq', form_def::factory('', '', form_def::select)->initMore(new \Sooh\Base\Form\Options(array('2147123124'=>'已禁用','0'=>'正常'),'不限')))
			->addItem('pageid', $pageid)
			->addItem('pagesize', $this->pager->page_size);
		
		$frm->fillValues($this->getInputs());

		if($frm->flgIsThisForm){
			$where = $frm->getWhere();
			$where['camefrom']='local';
		}else {
			$where=array();
		}
		$pager->init($this->manager->getAccountNum($where), $pageid);
		
		$isDownloadExcel = $this->_request->get('__EXCEL__')==1;
		
		$records = $this->manager->db()->getRecords($this->manager->tbname(),'*',$where,'sort camefrom sort loginname',$pager->page_size,$pager->rsFrom());
		$headers = array('账号'=>70,'昵称'=>90,'最后登入时间'=>80,'最后登入IP'=>70,'权限'=>'','状态'=>90,	);
		if(!$isDownloadExcel){
			$headers['其他操作']=90;
		}
		
		$menus = $this->manager->acl->getMenuEnum();
		$rightsMap=array();
		foreach($menus->children as $menu){
			foreach($menu->children as $child){
				$v = $child->options['_ModCtrl_'];
				$rightsMap[$v]=$menu->capt.'.'.$child->capt;
			}
		}
	
		if(!empty($records)){
			foreach($records as $rowid=>$r){
				$new = array($r['loginname'],$r['nickname'],date('m-d H:i',$r['lastDt']),$r['lastIP'],);
				
				$tmp = explode(',', $r['rights']);
				$ret = array();
				foreach($tmp as $k){
					list($i,$v) = explode('.', $rightsMap[$k]);
					$ret[$i][]=$v;
				}
				$tmp='';
				foreach($ret as $k=>$v){
					$tmp.= "【{$k}】:".implode(',', $v).' ';
				}
				
				$new[] = $tmp;
				$new[] = $r['dtForbidden']?'禁用':'正常';
				if(!$isDownloadExcel){
					$new[] ="<a class=\"button\" rel=\"dlg_default\" title=\"日志记录窗口\" target=\"dialog\" href=\"".\Sooh\Base\Tools::uri(array('_pkey_val'=>$pkey),'showlog')."\"><span>看日志</span></a>";
					$new['_pkey_val_']  = \Lib\Misc\DWZ::encodePkey(array('camefrom'=>'local','loginname'=>$r['loginname']));
				}
				$records[$rowid]=$new;
			}
		}

		if($isDownloadExcel){
			return $this->downExcel($records,  array_keys($headers));
		}else{
			$this->_view->assign('headers',$headers);
			$this->_view->assign('records',$records);
			$this->_view->assign('pager',$pager);
		}
			
		
	
	}
	
	/**
	 * 构建tree形式的权限的input
	 * @param \Sooh\Base\Acl\Menu $menu
	 * @param \Sooh\Base\Acl\Menu $child
	 */	
	public function renderTreeCheckbox($menu,$child)
	{
		
		$str ='<ul class="tree treeFolder treeCheck expand" >';
		$menus = $this->manager->acl->getMenuEnum();
		foreach($menus->children as $menu){
			$str .= "<li><a >{$menu->capt}</a><ul>";
			foreach($menu->children as $child){
				$v = $child->options['_ModCtrl_'];
				$checked = in_array($v, $this->tmpVal) ? ' checked="true" ' : '';
				$str.='<li><a tname="rights[]" tvalue="'.$v.'" '.$checked.'>'.$child->capt.'</a></li>';
			}
			$str .= "</ul></li>";
		}
		$str.="</ul>";			
		$this->_view->assign('inputRights',$str);
		return '';
	}
	protected function randPwd()
	{
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";

		for($i=0;$i<8;$i++){
			$str.=$strPol[rand(0,62)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	}
	/**
	 * 负责添加，更新逻辑以及表单页面控制
	 * @return type
	 */
	public function formAction()
	{
		
		$where = \Lib\Misc\DWZ::decodePkey($this->_request->get('_pkey_val'));
		
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(), 'post', empty($where)?\Sooh\Base\Form\Broker::type_c:\Sooh\Base\Form\Broker::type_u);
		//$frm->addItem('camefrom', form_def::factory('', 'local', form_def::constval));
		
		if(empty($where)){
			$frm->addItem('loginname', form_def::factory('帐号', '', form_def::text))
				->addItem('nickname', form_def::factory('昵称', '', form_def::text));
			$frm->addItem('passwd', form_def::factory('初始密码', '', form_def::text));
			$this->_view->assign('FormOp',$op='添加');
		}else{
			$frm->addItem('loginname', form_def::factory('帐号', '', form_def::constval))
				->addItem('nickname', form_def::factory('昵称', '', form_def::text));
			$this->_view->assign('FormOp',$op='更新');
		}
		$frm->addItem('_pkey_val', '')
			->addItem('rights', form_def::factory('权限', '', array($this,'renderTreeCheckbox')));//->initMore(new \Sooh\Base\Form\Options($this->optionsOfRights()))

		$frm->fillValues($this->getInputs());
		
		if($frm->flgIsThisForm){//submit
			try{
				if($frm->type()==\Sooh\Base\Form\Broker::type_c){//add new manager
					$fields=$frm->getFields();
					if(is_array($fields['rights'])){
						$fields['rights']=  implode (',', $fields['rights']);
					}
					$acc = \Lib\Services\Manager::getInstance(null);
					$acc->register($fields['loginname'], $fields['passwd'], $fields['camefrom']='local', 
									array('rights'=>$fields['rights'],'nickname'=>$fields['nickname']));
					$randPwd = $fields['passwd'];
				}else {//update manager
					$fields=$frm->getFields();
					if(is_array($fields['rights'])){
						$fields['rights']=  implode (',', $fields['rights']);
					}
					//var_log($fields,'upd:');
					unset($fields['camefrom']);
					unset($fields['loginname']);
					$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
					$manager->load();
					foreach($fields as $k=>$v){
						$manager->setField($k, $v);
					}
					$manager->update();
					$randPwd=null;
				}

				$this->closeAndReloadPage($this->tabname('index'));
				$this->returnOK($op.'成功'.($randPwd?',密码:'.$randPwd:''));

			}catch(\ErrorException $e){
				if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
					$this->returnError($op.'失败：冲突，相关记录已经存在？');
				}else{
					$this->returnError($op.'失败：'.$e->getMessage());
				}
			}
			
		}else{//show form
			if(!empty($where)){
				$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
				$manager->load();
				$fields=$manager->dump();
				$this->tmpVal = \Sooh\Base\Acl\Ctrl::_fromString($manager->getField('rights'));
				$ks = array_keys($frm->items);
				foreach($ks as $k){
					if(isset($fields[$k]) && is_object($frm->items[$k])){
						$frm->item($k)->value = $fields[$k];
					}
				}
				$frm->items['_pkey_val'] = \Lib\Misc\DWZ::encodePkey(array('camefrom'=>$fields['camefrom'],'loginname'=>$fields['loginname']));
			}else {
				$fields=array();
				$this->tmpVal=array();
				//$frm->item('camefrom')->value='local';
			}
		}
	}
	protected $tmpVal;
	/**
	 * 重置某账号密码
	 */
	
	public function pwdresetAction()
	{
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(), 'post', \Sooh\Base\Form\Broker::type_c);
		//$frm->addItem('camefrom', form_def::factory('', 'local', form_def::constval));
		$frm->addItem('loginname', form_def::factory('账号', '', form_def::constval));
		$frm->addItem('nickname', form_def::factory('昵称', '', form_def::text));
		$frm->addItem('passwd', form_def::factory('新密码', '', form_def::text));
		$this->_view->assign('FormOp',$op='修改');
		$frm->addItem('_pkey_val', '');

		$frm->fillValues($this->getInputs());
		
		$where = \Lib\Misc\DWZ::decodePkey($this->_request->get('_pkey_val'));
		
		if($frm->flgIsThisForm){//submit
			try{
				$fields=$frm->getFields();
				$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
				$manager->load();
				$ret = $manager->resetPWD($fields['passwd'],array('nickname'=>$fields['nickname']));
				if($ret){
					$this->returnOK('密码已重置为: '.$fields['passwd']);
				}else{
					$this->returnError('密码重置失败');
				}
				$this->closeAndReloadPage();
			}catch(\ErrorException $e){
				$this->returnError('密码重置失败:'.$e->getMessage());
			}
			
		}else{//show form
			if(!empty($where)){
				$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
				$manager->load();
				$fields=$manager->dump();
				$ks = array('loginname','nickname');
				foreach($ks as $k){
					if(isset($fields[$k]) && is_object($frm->items[$k])){
						$frm->item($k)->value = $fields[$k];
					}
				}
				$frm->items['_pkey_val'] = \Lib\Misc\DWZ::encodePkey(array('camefrom'=>$where['camefrom'],'loginname'=>$where['loginname']));
			}else {
				$this->returnError('unknown manager');
			}
		}
	}
	public function showlogAction()
	{
		$where = \Lib\Misc\DWZ::decodePkey($this->_request->get('_pkey_val'));
		$records = $this->getManagerLog(0, 0);
		$this->_view->assign('records',$records);
	}	
	/**
	 * 禁用某账号
	 */
	public function disableAction()
	{
		$where = \Lib\Misc\DWZ::decodePkey($this->_request->get('_pkey_val'));
		$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
		$manager->load();
		
		
		if(!empty($where)){
			try{
				$manager->setField('dtForbidden', 2147123124);
				$manager->update();
				$ret=true;
			}catch(\ErrorException $e){
				$ret=false;
			}
		}else{
			$ret=false;
		}
		if($ret){
			$this->returnOK('已禁用');
		}else {
			$this->returnError('禁用失败，请联系技术人员');
		}
	}
	/**
	 * 启用某账号
	 */
	public function enableAction()
	{
		$where = \Lib\Misc\DWZ::decodePkey($this->_request->get('_pkey_val'));
		$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
		$manager->load();
		
		
		if(!empty($where)){
			try{
				$manager->setField('dtForbidden', 0);
				$manager->update();
				$ret=true;
			}catch(\ErrorException $e){
				$ret=false;
			}
		}else{
			$ret=false;
		}

		if($ret){
			$this->returnOK('已启用');
		}else {
			$this->returnError('删除失败');
		}
	}		
}
