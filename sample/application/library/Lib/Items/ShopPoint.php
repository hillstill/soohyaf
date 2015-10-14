<?php
namespace Lib\Items;

/**
 * 积分(商城）
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ShopPoint implements \Lib\Items\Interfaces
{
	const fieldUser = 'shopPoints';
	const nLeft='n';
	/**
	 * 工厂函数构造实例的时候用的
	 * @param string $ext ： 没有使用
	 * @param string $userIdentifier
	 * @return \Lib\Items\ShopPoint
	 */
	public static function createByFactory($ext,$userIdentifier)
	{
		$ext = new ShopPoint($userIdentifier);
		return $ext;
	}
	/**
	 * 发放道具时默认的其他参数，比如有效期之类的，格式由各个道具类自行定义
	 * @param array $arrConfig
	 */
	public static function iniDefaultForGive($arrConfig)
	{
		if(is_array($arrConfig)){
			
		}
	}
	private $rs;
	private $_nleft;
	private $userIdentifier;
	public function __construct($userIdentifier) {
		$this->userIdentifier = $userIdentifier;
		$this->rs = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier)->getRecordsAll();
		$base=0;
		$chg = 0;
		foreach($this->rs as $r){
			if($r['finalStatus']===\Prj\Data\ShopPointLog::status_checkPoint){
				$base=$r['finalVal'];
			}elseif($r['finalStatus']!==\Prj\Data\ShopPointLog::status_cancel){
				$chg+=$r['changed'];
			}
		}
		$this->_nleft = $base+$chg;
	}
	/**
	 * 剩余情况(数量类型的道具返回剩余数量，其他返回剩余列表)
	 * @return int;
	 */	
	public function remain()
	{
		return $this->_nleft;
	}
	

	/**
	 * 把此道具发放给指定用户
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @throws \ErrorException
	 */
	public function giveToUser_prepare($num,$userIdentifier,$logMsg='byCheckin')
	{
		$belongTo = $this->userIdentiferBelongTo();
		if($belongTo && $belongTo!=$userIdentifier ){
			throw new \ErrorException(\Prj\ErrCode::errItemBelongSomeone);
		}
		if($this->log!=null){
			throw new \ErrorException('ShopPointLog exists');
		}
		
		$log = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier);
		$retry = 10;
		while($retry>0){

			$retry--;
			try{
				$nleft = $this->_nleft;
				$this->sn = $log->giveToUser($nleft,$num, $logMsg);
				$this->rs[$this->sn] = array(
					'changed'=>$num,'finalVal'=>$nleft+$num,'finalStatus'=>\Prj\Data\ShopPointLog::status_init,'statusIntro'=>$logMsg,
				);
				$this->_nleft+=$num;
				return;
			}  catch (\ErrorException $e){

			}
		}
		throw $e;
	}
	private $sn;
	/**
	 * 把此道具发放给指定用户(确认)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function giveToUser_confirm()
	{
		try{
			$log = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier);
			$ret = $log->updateStatus($this->sn, \Prj\Data\ShopPointLog::status_ok);
			if($ret!==1){
				error_log("confirm shopPoint to {$this->userIdentifier} failed(none changed):".\Sooh\DB\Broker::lastCmd()."\n");
				return false;
			}
			$this->rs[$this->sn]['finalStatus']=\Prj\Data\ShopPointLog::status_ok;
			return true;
		}  catch (\ErrorException $e){
			error_log("confirm shopPoint to {$this->userIdentifier} failed:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 发放失败，收回
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function giveToUser_rollback($logMsg)
	{
		try{
			$log = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier);
			$ret=$log->updateStatus($this->sn, \Prj\Data\ShopPointLog::status_cancel,$logMsg);
			if($ret!==1){
				error_log("confirm shopPoint to {$this->userIdentifier} failed(none changed):".\Sooh\DB\Broker::lastCmd()."\n");
				return false;
			}
			$this->rs[$this->sn]['finalStatus']=\Prj\Data\ShopPointLog::status_cancel;
			$this->_nleft-=$this->rs[$this->sn]['changed'];
			return true;
		}  catch (\ErrorException $e){
			error_log("confirm shopPoint to {$this->userIdentifier} failed:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
			return false;
		}
	}	
	/**
	 * 把此道具发放给指定用户(预发放，冻结状态)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @throws \ErrorException
	 */
	public function use_prepare($num,$userIdentifier,$logMsg)
	{
		$num=abs($num);
		$belongTo = $this->userIdentiferBelongTo();
		if($belongTo && $belongTo!=$userIdentifier ){
			throw new \ErrorException(\Prj\ErrCode::errItemBelongSomeone);
		}
		if($this->log!=null){
			throw new \ErrorException('ShopPointLog exists');
		}
		
		$log = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier);
		$retry = 10;
		while($retry>0){

			$retry--;
			try{
				$nleft = $this->_nleft;
				$this->sn = $log->giveToUser($nleft,-$num, $logMsg);
				$this->rs[$this->sn] = array(
					'changed'=>-$num,'finalVal'=>$nleft-$num,'finalStatus'=>\Prj\Data\ShopPointLog::status_init,'statusIntro'=>$logMsg,
				);
				$this->_nleft-=$num;
				return;
			}  catch (\ErrorException $e){

			}
		}
		throw $e;
	}
	/**
	 * 把此道具发放给指定用户(确认)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function use_confirm()
	{
		try{
			$log = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier);
			$ret = $log->updateStatus($this->sn, \Prj\Data\ShopPointLog::status_ok);
			if($ret!==1){
				error_log("confirm shopPoint to {$this->userIdentifier} failed(none changed):".\Sooh\DB\Broker::lastCmd()."\n");
				return false;
			}
			$this->rs[$this->sn]['finalStatus']=\Prj\Data\ShopPointLog::status_ok;
			return true;
		}  catch (\ErrorException $e){
			error_log("confirm shopPoint to {$this->userIdentifier} failed:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 发放失败，收回
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function use_rollback($logMsg)
	{
		try{
			$log = \Prj\Data\ShopPointLog::getCopy($this->userIdentifier);
			$ret=$log->updateStatus($this->sn, \Prj\Data\ShopPointLog::status_cancel,$logMsg);
			if($ret!==1){
				error_log("confirm shopPoint to {$this->userIdentifier} failed(none changed):".\Sooh\DB\Broker::lastCmd()."\n");
				return false;
			}
			$this->rs[$this->sn]['finalStatus']=\Prj\Data\ShopPointLog::status_cancel;
			$this->_nleft-=$this->rs[$this->sn]['changed'];
			return true;
		}  catch (\ErrorException $e){
			error_log("confirm shopPoint to {$this->userIdentifier} failed:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString());
			return false;
		}
	}
	/**
	 * 该道具属于哪个用户
	 * @return string userIdentifier
	 */
	public function userIdentiferBelongTo()
	{
		return $this->userIdentifier;
	}
	/**
	 * 获得道具的类名
	 * @return string itemname
	 */
	public function itemclass()
	{
		return 'ShopPoint';
	}
	/**
	 * 所有记录
	 * @return  array 
	 */
	public function all($pageSize,$rsFrom=0,$sort=null,$where=null)
	{
		throw new \ErrorException('todo');
	}
}
