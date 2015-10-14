<?php
namespace Lib\Items;
/**
 * 道具类通用接口
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
interface Interfaces{
	/**
	 * 工厂函数构造实例的时候用的
	 * @param string $ext
	 * @param string $userIdentifier
	 * @return \Lib\Items\Interfaces
	 */
	public static function createByFactory($ext,$userIdentifier);
	
	/**
	 * 发放道具时默认的其他参数，比如有效期之类的，格式由各个道具类自行定义
	 * @param array $arrConfig
	 */
	public static function iniDefaultForGive($arrConfig);
	
	/**
	 * 把此道具发放给指定用户(预发放，冻结状态)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @throws \ErrorException
	 */
	public function giveToUser_prepare($num,$userIdentifier,$logMsg);
	/**
	 * 把此道具发放给指定用户(确认)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function giveToUser_confirm();
	/**
	 * 发放失败，收回
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function giveToUser_rollback($logMsg);
	
	/**
	 * 把此道具发放给指定用户(预发放，冻结状态)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @throws \ErrorException
	 */
	public function use_prepare($num,$userIdentifier,$logMsg);
	/**
	 * 把此道具发放给指定用户(确认)
	 * @param int $num
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function use_confirm();
	/**
	 * 发放失败，收回
	 * @param string $userIdentifier
	 * @param string $logMsg 日志记录说明
	 * @return bool
	 */
	public function use_rollback($logMsg);
	/**
	 * 剩余情况(数量类型的道具返回剩余数量，其他返回剩余列表)
	 * @return mix;
	 */
	public function remain();

	/**
	 * 该道具属于哪个用户
	 * @return string userIdentifier
	 */
	public function userIdentiferBelongTo();
	/**
	 * 获得道具的类名
	 * @return string itemname
	 */
	public function itemclass();
	/**
	 * 历史流水
	 * @param int $pageSize 
	 * @param int $rsFrom 
	 * @param string $sort 
	 * @return  array 
	 */
	public function all($pageSize,$rsFrom=0,$sort=null,$where=null);
	
	
}
