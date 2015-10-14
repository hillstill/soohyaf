<?php
namespace Prj\Acl;
class Manage extends \Sooh\Base\Acl\Ctrl
{
	protected function initMenu()
	{
		return array(
			'一级菜单a.二级菜单-01'=>array('manage','iosnatureworth1', 'test1',array(),array()),
			'一级菜单a.二级菜单-02'=>array('manage','iosnatureworth2', 'tst2',array(),array()),
			
			'一级菜单b.baidu-11'=>array('manage','iosnatureworth3','test3', array(),array('external'=>true)),
			
			'系统管理.管理员一览'=>array('manage','managers','index',array(),array()),
		);
	}
}