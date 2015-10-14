<?php
namespace Lib\Items;
/**
 * Description of Item
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Broker {
	/**
	 * 预发放道具
	 * @param array $bonusThisTime
	 * @param string $accountId
	 * @param \Lib\Items\Interfaces $_ignore_
	 * @return type
	 */
	public static function batchGive_pre($bonusThisTime,$accountId,$_ignore_=null)
	{
		$givedThisTime=array();
		foreach($bonusThisTime as $itemname=>$num){
			$_ignore_ = self::create($itemname,$accountId);
			try {
				
				$_ignore_->giveToUser_prepare($num,$accountId,'签到奖励');
				$givedThisTime[]=$_ignore_;
			} catch (\ErrorException $ex) {
				error_log("give bonus $itemname to $accountId failed as ".$ex->getMessage()."\n".$ex->getTraceAsString());
				$_ignore_->giveToUser_rollback('trans_rollback');
				break;
			}
		}
		$remain = $_ignore_->remain();
		if(is_array($remain)){
			$remain = sizeof($remain);
		}
		\Sooh\Base\Log\Data::getInstance()->setThese('', '', $itemname, $num, $remain);
		return $givedThisTime;
	}
	/**
	 * 确认道具发放
	 * @param array $givedThisTime
	 * @param string $accountId
	 * @param \Lib\Items\Interfaces $_ignore_
	 * @return boolean
	 */
	public static function batchGive_confirm($givedThisTime,$accountId,$_ignore_=null)
	{
		$allOk=true;
		foreach($givedThisTime as $_ignore_){
			if(false === $_ignore_->giveToUser_confirm()){
				\Prj\Loger::alarm('give '.$_ignore_. ' ToUser('.$accountId.')_rollback failed');
				$allOk=false;
			}
		}
		return $allOk;
	}
	/**
	 * 道具发放回滚
	 * @param 原因说明 $msg
	 * @param array $givedThisTime
	 * @param string $accountId
	 * @param \Lib\Items\Interfaces $_ignore_
	 * @return boolean
	 */
	public static function batchGive_rollback($msg,$givedThisTime,$accountId,$_ignore_=null)
	{
		$allOk=true;
		foreach($givedThisTime as $_ignore_){
			if(false===	$_ignore_->giveToUser_rollback($msg)){
				\Prj\Loger::alarm('give '.$_ignore_. ' ToUser('.$accountId.')_rollback failed');
				$allOk=false;
			}
		}
		return $allOk;
	}
	/**
	 * 创建一个道具(预定发放给$userIdentifier)
	 * @param string $item
	 * @param string $userIdentifier
	 * @return \Lib\Items\Interfaces
	 */
	public static function create($item,$userIdentifier)
	{
		$r = explode('_',$item);
		$classname = '\\Lib\\Items\\'.  array_shift($r);
		$ext = array_shift($r);
		return $classname::createByFactory($ext,$userIdentifier);
	}
	/**
	 * 道具发放前默认参数设置调整（比如不同途径发放的券的有效期不同）
	 * @param type $item
	 * @param type $arrConfig
	 * @return type
	 */
	public static function iniForGiven($item,$arrConfig)
	{
		$r = explode('_', $item);
		$classname = '\\Prj\\Items\\'.$r[0];
		if(is_string($arrConfig)){
			$ch = substr($arrConfig, 0,1);
			if($ch==='[' || $ch==='{'){
				$arrConfig = json_decode($arrConfig,true);
			}else{
				$tmp = $arrConfig;
				parse_str($tmp, $arrConfig);
			}
		}
		return $classname::iniDefaultForGive($arrConfig);
	}
}
