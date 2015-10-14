<?php
namespace Prj;
/**
 * Description of Loger
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Loger {
	public static function alarm($msg)
	{
		error_log("[ALARM]$msg");
	}
	
	public static function trace($msg)
	{
		error_log("[Trace]$msg");
	}
}
