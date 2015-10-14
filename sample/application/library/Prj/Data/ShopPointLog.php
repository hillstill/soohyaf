<?php
namespace Prj\Data;
/**
 * 积分流水日志
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ShopPointLog extends \Sooh\DB\Base\KVObj {
	const status_ok = 200;
	const status_init=0;
	const status_cancel=100;
	const status_checkPoint=999;
	public function giveToUser($curLeft,$num,$msg)
	{
		$mod = self::indexForSplit($this->pkey);
		$dt = \Sooh\Base\Time::getInstance()->timestamp();
		
		$this->setField('changed', $num);
		$this->setField('finalval', $curLeft+$num);
		$this->setField('finalstatus', self::status_init);
		$this->setField('descAdd', $msg);
		$this->setField('dtAdd', $dt);
		for($retry=0;$retry<10;$retry++){
			try{
				$sn=$dt. sprintf("%05d",rand(0,99999)). sprintf("%04d",$mod);
				$this->pkey['ShopPointOSN']=$sn;
				$this->update();
				return $sn;
			}catch(\ErrorException $e){
				
			}
		}
		throw new \ErrorException(\Sooh\Base\ErrException::msgServerBusy);
	}

	public function updateStatus($sn,$status,$msg=null)
	{
		$chg = array('finalStatus'=>$status);
		if($msg!=null){
			if($status==self::status_init){
				$chg['descAdd']=$msg;
			}else{
				$chg['descUse']=$msg;
			}
		}
		return $this->db()->updRecords($this->tbname(),$chg ,array('userIdentifier'=>$this->pkey['userIdentifier'],'ShopPointOSN'=>$sn));
	}
	
	public function getRecordsAll()
	{
		$db = $this->db();
		$tb = $this->tbname();
		$snOfCheckPoint = $db->getOne($tb, 'ShopPointOSN',array('userIdentifier'=>$this->pkey['userIdentifier'],'finalStatus'=>self::status_checkPoint),'rsort ShopPointOSN');
		if($snOfCheckPoint){
			return  $this->db()->getAssoc($this->tbname(), 'ShopPointOSN','changed,finalVal,finalStatus,descAdd,dtAdd,descUse,dtUse',array('userIdentifier'=>$this->pkey['userIdentifier'],'ShopPointOSN]'=>$snOfCheckPoint));
		}else{
			return  $this->db()->getAssoc($this->tbname(),'ShopPointOSN', 'changed,finalVal,finalStatus,descAdd,dtAdd,descUse,dtUse',array('userIdentifier'=>$this->pkey['userIdentifier']));
		}
	}
	protected function createTable()
	{
		$this->db()->ensureObj($this->tbname(), array(
			'userIdentifier'=>'varchar(64) not null',
			'ShopPointOSN'=>'bigint unsigned not null default 0',
			'changed'=>'int not null default 0',
			'finalVal'=>'int not null default 0',
			'finalStatus'=>'int not null default 0',
			'descAdd'=>"varchar(100) not null default ''",
			'dtAdd'=>'int not null default 0',
			'descUse'=>"varchar(100) not null default ''",
			'dtUse'=>'int not null default 0',
			'iRecordVerID'=>'bigint not null default 0'
		),array('userIdentifier','ShopPointOSN'));
	}
	/**
	 * 
	 * @param string $userIdentifier
	 * @return \Prj\Data\ShopPointLog
	 */
	public static function getCopy($userIdentifier) {
		return parent::getCopy(array('userIdentifier'=>$userIdentifier,'ShopPointOSN'=>0));
	}
	/**
	 * 仅根据userIdentifier计算分表用的indexId
	 */	
	protected static function indexForSplit($pkey)
	{
		$tmp = $pkey['userIdentifier'];
		if(is_numeric($tmp)){
			return substr($tmp,-4);
		}else{
			$s = substr($pkey['userIdentifier'],-3);
			$n1 = base_convert($s, 16, 10)%100;
			$s = substr($pkey['userIdentifier'],6,3);
			$n2 = base_convert($s, 16, 10)%100;
			return $n2*100+$n1;
		}
		
	}
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default'.($isCache?'Cache':'');
	}	
	public static function numToSplit() {
		if(\Sooh\Base\Ini::getInstance()->get('deploymentCode')<=20){
			return 2;
		}else{
			return 2;
		}
	}
	
//	/**
//	 * 使用log库
//	 */
//	protected static function idFor_dbByObj_InConf($isCache)
//	{
//		return 'use_db_log';
//	}
	/**
	 * 使用哪个表
	 */
	protected static function splitedTbName($n,$isCache)
	{
		return 'tb_shopPoint_'.($n%static::numToSplit());
	}
	
	
	
	public function update($callback = null) {
		try{
			parent::update($callback);
		} catch ( \Sooh\DB\Error $e) {
			if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::tableNotExists)){
				$this->createTable ();
				parent::update($callback);
			}else {
				throw $e;
			}
		}
	}
}
