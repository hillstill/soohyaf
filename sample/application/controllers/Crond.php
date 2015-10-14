<?php

/**
 * 定时任务
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CrondController  extends \Prj\BaseCtrl {
	/**
	 * 检查，必须是通过crond.php执行的
	 */
	public function init()
	{
		$this->ini = \Sooh\Base\Ini::getInstance();
		if($this->ini->get('released')){
			if(SOOH_INDEX_FILE!=='crond.php'){
				die('not start with crond');
			}
		}
	}
	/**
	 * 手动执行
	 */
	public function runAction()
	{
		$ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Cronds", "__=crond/hourly", \Lib\Services\CrondLog::getInstance($this->getRpcDefault('CrondLog')));
		$ctrl->initNamespace('PrjCronds');
		$ctrl->runManually($this->_request->get('task'), $this->_request->get('ymdh'));
	}
	/**
	 * 每小时自动执行
	 */
	public function  hourlyAction()
	{
		$ctrl = new \Sooh\Base\Crond\Ctrl(APP_PATH."/application/Cronds", "__=crond/hourly",\Lib\Services\CrondLog::getInstance($this->getRpcDefault('CrondLog')));
		$ctrl->initNamespace('PrjCronds');
		$ctrl->runCrond($this->_request->get('task'), $this->_request->get('ymdh'));
	}	
}
