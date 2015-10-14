<?php
namespace Lib\Services;

/**
 * Manager
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Manager extends \Sooh\Base\Acl\Account{
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Base $rpcOnNew
	 * @return Manager
	 */
	public static function getInstance($rpcOnNew=null)
	{
		return parent::getInstance($rpcOnNew);
	}	
	protected function setAccountStorage($accountname, $camefrom)
	{
		$this->account = \Prj\Data\Manager::getCopy($accountname, $camefrom);
	}
	
	public function addFirstAccount()
	{
		$accountname = 'root';
		$password = '123456';
		$camefrom = 'local';
		$customArgs=array('rights'=>'manage.managers');
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('accountname'=>$accountname,'password'=>$password,'camefrom'=>$camefrom,'customArgs'=>$customArgs))->send('register');
		}else{
			$this->setAccountStorage($accountname, $camefrom);
			$n =$this->account->getAccountNum(array()); 
			if($n>0){
				return 'skip';
			}else{
				$this->register($accountname, $password, $camefrom, $customArgs);
				return 'add first user:root';
			}
		}
	}
}
