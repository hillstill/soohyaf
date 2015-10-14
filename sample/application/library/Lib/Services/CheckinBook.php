<?php
namespace Lib\Services;
/**
 * 签到簿
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CheckinBook {
	const errTodayDone='今天已经签到过了';
	const errMonthDone='本月已经签完了';
	const msgCheckinDone='签到成功';
	const maxMonth=21;
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Base $rpcOnNew
	 * @return CheckinBook
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			self::$_instance = new CheckinBook;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker 
	 */
	protected $rpc=null;
	public function decode($var)
	{
		
		if(is_string($var)){
			$this->r = json_decode($var,true);
		}elseif (is_array($var)) {
			$this->r = $var;
		}else{
			$this->r = array();
		}
		
		$this->today = \Sooh\Base\Time::getInstance()->YmdFull;
		$ym = floor($this->today/100);
		
		if(floor($this->r['ymd']/100)!=$ym){
			$this->r['ymd']=$ym*100;
			$this->r['bonusGot']=array();
			$this->r['checked']=array(0);
		}
	}
	
	const fieldUser = 'checkin_book';
	private $r;

	public function doDebugReset($ymd,$num=-1,$userIdentifier=null)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('ymd'=>$ymd,'num'=>$num,'userIdentifier'=>$userIdentifier))->send(__FUNCTION__);
		}else{
			$this->r['ymd']=$ymd;
			if($num!=-1){
				if($num==0){
					$this->r['checked']=array(0);
				}else{
					$this->r['checked']=array();
					for($i=0;$i<$num;$i++){
						$this->r['checked'][]=1;
					}
				}
			}
		}
	}
	/**
	 * 获取当日情况： [是否已经签到：checked=>array(1,1,1,0,0,0,0,), todaychked=0|1,ymd=>20150501, bonusList=>array(array(item1=>num1),...30=>array(item1=>num1),)]
	 * 注意日期列表都是下标0开始的数组
	 * @param boolean $withBonus 返回里是否带奖励物品列表
	 * @param \Prj\Data\User $userOrAccountId accountId 或 \Prj\Data\User
	 * @return array
	 */
	public function doGetTodayStatus($withBonus,$userOrAccountId)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('withBonus'=>$withBonus,'userOrAccountId'=>$userOrAccountId))->send(__FUNCTION__);
		}else{
			if(is_scalar($userOrAccountId)){
				$userOrAccountId = \Prj\Data\User::getCopy($userOrAccountId);
			}
			$userOrAccountId->load();
			$this->decode($userOrAccountId->getField(self::fieldUser,true));
			
			return $this->returnThese($withBonus);
		}
	}
	/**
	 * 签到
	 * @param boolean $withBonus 返回里是否带奖励物品列表
	 * @param \Prj\Data\User $userOrAccountId accountId 或 \Prj\Data\User
	 * @return array
	 */	
	public function doCheckIn($withBonus,$userOrAccountId)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('withBonus'=>$withBonus,'userOrAccountId'=>$userOrAccountId))->send(__FUNCTION__);
		}else{
			if(is_scalar($userOrAccountId)){
				$userOrAccountId = \Prj\Data\User::getCopy($userOrAccountId);
			}
			$userOrAccountId->load();
			if($userOrAccountId->exists()===false){
				$userOrAccountId->setField(self::fieldUser, array());
				$userOrAccountId->update();
			}
			$this->decode($userOrAccountId->getField(self::fieldUser,true));
			
			if($this->r['ymd']==$this->today){
				\Sooh\Base\Log\Data::getInstance()->ret = "checkin already";
				return $this->errFound(self::errTodayDone,400, $withBonus);
			}elseif(sizeof($this->r['checked'])>=self::maxMonth){
				\Sooh\Base\Log\Data::getInstance()->ret = 'checkin of this month:all done';
				return $this->errFound(self::errMonthDone,400, $withBonus);
			}
			
			$accountId = $userOrAccountId->getAccountId();
			$idCheckThisTime=array_sum($this->r['checked']);
			$bonusThisTime = $this->getBonusList()[$idCheckThisTime];
			if(false===$userOrAccountId->lock('chkinBonus:'.http_build_query($bonusThisTime))){
				\Sooh\Base\Log\Data::getInstance()->ret = "lock user for checkin failed";
				\Prj\Loger::alarm('[LockFailed] user-table on checkin'.\Sooh\DB\Broker::lastCmd());
				return $this->errFound(\Sooh\Base\ErrException::msgServerBusy,500, $withBonus);
			}
			
			//\Lib\Items\Broker::iniForGiven('Voucher', array('iniDefaultForGive_a'=>60));// 如果有道具发放时有额外参数要设置（比如有效期不同）
			$givedThisTime = \Lib\Items\Broker::batchGive_pre($bonusThisTime,$accountId);
			
			if(sizeof($givedThisTime)==  sizeof($bonusThisTime)){
				if(\Lib\Items\Broker::batchGive_confirm($givedThisTime,$accountId)){
					try{
						$this->r['checked'][$idCheckThisTime]=1;
						$this->r['bonusGot'][$idCheckThisTime]=$bonusThisTime;
						$this->r['ymd']=$this->today;
						$userOrAccountId->setField(self::fieldUser, $this->r);
						$userOrAccountId->update();
						\Sooh\Base\Log\Data::getInstance()->ret = "done";
						
						return $this->allDone(self::msgCheckinDone, $withBonus);
					}  catch (\ErrorException $errOnUpdate){
						\Prj\Loger::alarm('[CheckInFailed] on update user:'.$errOnUpdate->getMessage());
					}
				}
			}
			if(!empty($errOnUpdate)){
				\Prj\Loger::alarm('[GiveItemFailed] on checkin');
				\Sooh\Base\Log\Data::getInstance()->ret = "give item failed";
			}else{
				\Sooh\Base\Log\Data::getInstance()->ret = "update user failed";
			}
			if(\Lib\Items\Broker::batchGive_rollback('GiveItemFailedOnCheckin',$givedThisTime,$accountId)){
				$userOrAccountId->unlock();
				return $this->errFound(\Sooh\Base\ErrException::msgServerBusy, 500, $withBonus);
			}else{
				return $this->errFound(\Sooh\Base\ErrException::msgServerBusy, 500, $withBonus);
			}
		}
	}
	protected $today =19770101;
	protected $bonus=array();
	protected function errFound($msg,$code,$withBonus)
	{
		$r = $this->returnThese($withBonus);
		$r['code']=$code;
		$r['msg']=$msg;
		return $r;
	}
	protected function allDone($msg,$withBonus)
	{
		$r = $this->returnThese($withBonus);
		$r['code']=200;
		$r['msg']=$msg;
		return $r;
	}
	protected function returnThese($withBonus)
	{
		$ret = array('ymd'=>$this->today,'checked'=>$this->r['checked']);
		if(sizeof($this->r['checked'])>=21){
			$ret['todaychked']=true;
		}else{
			$ret['todaychked'] = $this->r['ymd']==$this->today?1:0;
		}
		if($withBonus){
			$ret['bonusList'] = $this->getBonusList();
		}
		return array('data'=>$ret);
	}
	/**
	 * 0下标开始的当月签到奖励, 根据需要（比如本月累计签到了几次）调整
	 * @return type
	 */
	protected function getBonusList()
	{
		$default=array();
		for($i=0;$i<21;$i++){
			$default[$i]=array('ShopPoint'=>floor($i/2)+1);
		}
		$fetched = $this->r['bonusGot'];
		$activesFrom = strtotime(20150608);
		$activesTo = strtotime(20150619);//18日是最后一天的话，这里填19日
		$today = strtotime(\Sooh\Base\Time::getInstance()->YmdFull);
		if($today<$activesFrom){
			$keep=($activesFrom-$today)/86400;
			$dbl = ($activesTo-$activesFrom)/86400;
		}else{
			$keep=0;
			if($today<=$activesTo){
				$dbl = ($activesTo-$today)/86400;
			}
		}
		foreach($default as $k=>$v){
			if(!isset($fetched[$k])){
				if($keep>0){
					$fetched[$k]=$v;
					$keep--;
				}elseif($dbl>0){
					$dbl--;
					if(key($v)==='ShopPoint'){
						$v[key($v)]*=2;
						$fetched[$k]=$v;
					}else{
						$fetched[$k]=$v;
					}
				}else{
					$fetched[$k]=$v;
				}
			}
		}
		return $fetched;
	}
}
