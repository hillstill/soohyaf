<?php
namespace PrjCronds;
/**
 * 检查失败的订单，回复本金券状态
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Test extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=300;
		$this->ret = new \Sooh\Base\Crond\Ret();
	}
	public function free() {
		parent::free();
	}
	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		
		if($this->_isManual){
			$m=1;
		}else{
			$m=10;
		}

		$this->lastMsg = "m=$m";
		return true;

	}
}
