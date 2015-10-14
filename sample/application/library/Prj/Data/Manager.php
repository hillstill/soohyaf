<?php
namespace Prj\Data;
/**
 * Description of User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Manager  extends \Sooh\DB\Cases\AccountStorage{
	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return Manager
	 */
	public static function getCopy($account, $camefrom = 'local') {
		return parent::getCopy($account, $camefrom);
	}
	/**
	 * 
	 * @param string $managerId
	 * @return Manager
	 */
	public static function getCopyByManagerId($managerId) {
		$rs = static::loopFindRecords(array('accountId'=>$managerId));
		if(!empty($rs)){
			return parent::getCopy($rs[0]['loginname'], $rs[0]['camefrom']);
		}else{
			throw new \Sooh\Base\ErrException('manager not found');
		}
	}	
	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_managers_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'manage';
	}
	//针对缓存，非缓存情况下具体的表的名字

	//说明分几张表
	protected static function numToSplit(){return 1;}
	public function getAccountNum($where)
	{
		return static::loopGetRecordsCount($where);
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
	/**
	 * 
	 * @var \Sooh\Base\Acl\Ctrl
	 */
	public $acl;
	public function load($fields='*')
	{
		$ret = parent::load($fields);
		$this->acl = \Prj\Acl\Manage::getInstance();
		//TODO: manager-rights const
		$this->acl->fromString($this->getField('rights',true));
		return $ret;
	}
}
