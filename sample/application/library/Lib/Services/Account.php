<?php
namespace Lib\Services;

/**
 * Account
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Account extends \Sooh\Base\Acl\Account{
	protected function setAccountStorage($accountname, $camefrom)
	{
		$this->account = \Prj\Data\Account::getCopy($accountname, $camefrom);
	}
}
