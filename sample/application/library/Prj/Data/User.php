<?php
namespace Prj\Data;
/**
 * Description of User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class User  extends \Sooh\DB\Base\KVObj{
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'default'.($isCache?'Cache':'');
	}
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_users_'.($n % static::numToSplit());
	}
	//说明分几张表
	protected static function numToSplit(){
		if(\Sooh\Base\Ini::getInstance()->get('deploymentCode')<=20){
			return 1;
		}else{
			return 1;
		}
	}

	/**
	 * 说明getCopy实际返回的类，同时对于只有一个主键的，可以简化写法
	 * @return \Prj\Data\User
	 */
	public static function getCopy($accountId)
	{
		return parent::getCopy(array('accountId'=>$accountId));
	}
	
	public function getAccountId()
	{
		return current($this->pkey);
	}
//	/**
//	 * 是否启用cache机制
//	 * cacheSetting=0：不启用
//	 * cacheSetting=1：优先从cache表读，每次更新都先更新硬盘表，然后更新cache表
//	 * cacheSetting>1：优先从cache表读，每次更新先更新cache表，如果达到一定次数，才更新硬盘表
//	 */
//	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
//	{
//		return parent::initConstruct($cacheSetting,$fieldVer);
//	}
}
