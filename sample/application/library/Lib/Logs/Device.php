<?php
namespace Lib\Logs;

/**
 * 设备使用情况记录和追踪
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Device extends \Sooh\DB\Base\KVObj {
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default';
	}
	public static function guidMaker($type,$sn)
	{
		if(empty($sn)){
			$sn = md5(microtime(true).\Sooh\Base\Tools::remoteIP());
		}else{
			$pre = substr($sn,0,5);
			if(in_array($pre, array('idfa:','imei:','mac :','md5 :'))){
				$sn=substr($sn,5);
			}
		}
		switch($type){
			case 'idfa':return 'idfa:'.$sn;
			case 'imei':return 'imei:'.$sn;
			case 'mac':	return 'mac :'.$sn;
			case 'md5': return 'md5 :'.$sn;
			case 'www': return 'www :'.$sn;
			case 'wap': return 'wap :'.$sn;
			default:
				error_log('error: unknown device type found:'.$type);
				$len = strlen($type);
				switch ($len){
					case 0: throw new \ErrorException('type of device not given');
					case 1: return $type.'   :'.$sn;
					case 2: return $type.'  :'.$sn;
					case 3: return $type.' :'.$sn;
					default:return substr($type,0,4).' :'.$sn;
				}
		}
	}
	/**
	 * 
	 * @param string $deviceId idfa:xxxxx
	 * @return \PrjLib\DataDig\Log\Device
	 */
	public static function getCopy($deviceId) {
		return parent::getCopy(array('deviceId'=>$deviceId));
	}
	
	public function getDeviceId()
	{
		return $this->pkey['deviceId'];
	}
	public $flgNewCreate=false;
	/**
	 * 
	 * @param string $type [idfa|imei|md5|mac]
	 * @param string $sn
	 * @param string $phone
	 * @param string $userIdentifier
	 * @param string $contractId
	 * @param array $extraData
	 * @return \PrjLib\DataDig\Log\Device
	 */
	public static function ensureOne($type,$sn,$phone=null,$userIdentifier=null,$contractId=null,$extraData=null)
	{
		$dt = \Sooh\Base\Time::getInstance();
		$deviceId = self::guidMaker($type, $sn);
		$ddd = \Yaf_Dispatcher::getInstance()->getRequest();
		error_log("trace device->ensure(".$ddd->getModuleName().'/'.$ddd->getControllerName().'/'.$ddd->getActionName().") ".$deviceId. "  phone:$phone  user:$userIdentifier");
		$sys = parent::getCopy(array('deviceId'=>$deviceId));
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$sys->load();
		}  catch (\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
				$sys->createTable ();
			}
		}
		$fields = array(
			//'phone'=>$phone,
			//'userIdentifier'=>$userIdentifier,
			'ip'=>\Sooh\Base\Tools::remoteIP(),
			'ymd'=>$dt->YmdFull,
			'hhiiss'=>$dt->his,
		);
		try{
			if($sys->exists()===false){
				foreach($fields as $k=>$v){
					$sys->setField($k, $v);
				}
				$sys->setField('phone', empty($phone)?'0':$phone);
				$sys->setField('userIdentifier', empty($userIdentifier)?'':$userIdentifier);
				$sys->setField('extraData', (empty($extraData)?'':json_encode($extraData)));
				$sys->setField('extraRet', '');
				$sys->setField('contractId', empty($contractId)?'0':$contractId);
				try{
					\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::duplicateKey);
					$sys->update();
					$sys->flgNewCreate=true;
					return $sys;
				}catch(\ErrorException $e){
					$sys->reload();
					if($sys->exists()===false){
						error_log('error create new device log:'.$e->getMessage()."\n".$e->getTraceAsString());
						return $sys;
					}
				}
			}
			
			$oldPhone = $sys->getField('phone',true);
			$oldUser = $sys->getField('userIdentifier',true);
			$oldContractId = $sys->getField('contractId',true);

			if((!empty($phone) && !empty($oldPhone) && $phone!=$oldPhone) || (!empty($oldUser) && !empty($userIdentifier) && $oldUser!=$userIdentifier) || (!empty($oldContractId) && !empty($contractId) && $oldContractId!=$contractId) ){
				$extraDataOld = $sys->getField('extraData',true);
				$extraRetOld = $sys->getField('extraRet',true);
				$extraRetOld = (is_scalar($extraRetOld)===false?json_encode($extraRetOld):$extraRetOld);
				\Sooh\DB\Broker::getInstance(\PrjLib\Tbname::db_rpt)->addRecord(\PrjLib\Tbname::tb_device_log, 
					array(
						'deviceId'=>$deviceId,
						'dtChange'=>$dt->YmdFull.  sprintf('%06d',$dt->his),
						'phoneOld'=>$oldPhone,
						'userIdentifierOld'=>$oldUser,
						'extraDataOld'=>(is_scalar($extraDataOld)===false?json_encode($extraDataOld):$extraDataOld),
						'extraRetOld'=>$extraRetOld,
						'contractIdOld'=>$oldContractId,
						'phoneNew'=>$phone,
						'userIdentifierNew'=>$userIdentifier,
						'extraDataNew'=>empty($extraData)?'':json_encode($extraData),
						'extraRetNew'=>$extraRetOld,
						'contractIdNew'=>empty($contractId)?'0':$contractId,
						'ipOld'=>$sys->getField('ip',true),
						'ipNew'=>$fields['ip'],
					)
				);
			}
			foreach($fields as $k=>$v){
				$sys->setField($k, $v);
			}
			if(!empty($extraData)){
				$sys->setField('extraData', (empty($extraData)?'':json_encode($extraData)));
			}
			if(!empty($phone)){
				$sys->setField('phone', $phone);
			}
			if(!empty($userIdentifier)){
				$sys->setField('userIdentifier', $userIdentifier);
			}
			//$sys->setField('extraRet', '');
			if(!empty($contractId)){
				$sys->setField('contractId', $contractId);
			}
			$sys->update();
		
			//var_log($sys->dump(),'======================log->filled for '.$sys->tbname());
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
		} catch ( \ErrorException $e) {
			error_log("error: on ensure-device:".$e->getMessage()."\n".$e->getTraceAsString());
		}
		return $sys;
	}
	
	/**
	 * 创建每天的日志表
	 */
	protected function createTable()
	{
		$this->db()->ensureObj($this->tbname(), array(
			'deviceId'=>'varchar(64) not null',
			'phone'=>'bigint not null default 0',
			'userIdentifier'=>'varchar(64) not null',
			'contractId'=>'bigint not null default 0',
			'extraData'=>'varchar(1000) not null',
			'extraRet'=>'varchar(500) not null',
			'ip'=>'varchar(32) not null',
			'ymd'=>'int not null default 0',
			'hhiiss'=>'int not null default 0',
			'iRecordVerID'=>'bigint not null default 0'
		),array('deviceId'));
	}
	protected static function splitedTbName($n,$isCache)
	{
		return 'tb_device_'.($n % static::numToSplit());
	}
	protected static function numToSplit(){return 100;}

}
