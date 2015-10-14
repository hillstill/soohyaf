<?php
/**
 * 签到系统
		返回
		{
			'code':200,
			'msg':'xxxxx',								如果有文字消息提示
			'checkinBook':{
				'ymd':20150501,                             请求对应的日期
				'todaychked':0,                                    请求的那天是否已经签到过了
				'checked':[0,1,1,1,0,0,0,0,......],         当月是否已经签到，数组，下标从0，0-30开始，对应1日-31日
				'bonusList':[                                     如果参数 with_bonus=1，提供此节点（奖励明细列表）, 下标处理同'checked'
						{"ShopPoint":1}，{"ShopPoint":1}，....{'VoucherInterest_500':1}            ShopPoint：积分；VoucherInterest_500：500元本金券
				]
			}
		}
 * 参数 : withbonus=[0|1] 默认0，    是否提供奖励明细列表
 * 
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CheckinController  extends \Prj\ApiCtrl {
	public function init() {
		parent::init();
		/**
		$this->userOrAccountId = $this->user;
		$this->book = \Lib\Services\CheckinBook::getInstance();
		 */
		$this->userOrAccountId = $this->user->getAccountId();
		$this->book = \Lib\Services\CheckinBook::getInstance($this->getRpcDefault('CheckinBook'));
	}
	private $userOrAccountId;

	/**
	 *
	 * @var \Lib\Services\CheckinBook 
	 */
	protected $book;
	/**
	 *
	 * @var \Lib\Items\ShopPoint  
	 */
	protected $shopPoint;
	/**
	 * 登入用户当天签到情况
	 * host/v1/checkin/today ?[withbonus=1]
	 */
	public function todayAction()
	{
		$this->_view->assign('checkinBook',$this->book->doGetTodayStatus($this->_request->get('with_bonus')-0,$this->userOrAccountId)['data']);
//		if($this->_request->get('withShoppoints')-0){
//			$shopPoints = new \Lib\Items\ShopPoint($this->user->getIdentifier());
//			$this->_view->assign('shopPoints',array('nLeft'=>$shopPoints->nLeft()));
//		}
	}	
	/**
	 * 签到
	 * host/v1/checkin/checkin ?[withbonus=1]
	 */
	public function  checkinAction()
	{
		$ret = $this->book->doCheckIn($this->_request->get('with_bonus')-0, $this->userOrAccountId);
		$this->_view->assign('code',$ret['code']);
		if(!empty($ret['msg'])){
			$this->_view->assign('msg',$ret['msg']);
		}
		$this->_view->assign('checkinBook',$ret['data']);
	}	
	/**
	 * 测试用
	 */
	public function resetdayAction()
	{
		if(\Sooh\Base\Ini::getInstance()->get('deploymentCode')<40){
			$this->book->doDebugReset($this->_request->get('ymd')-0,$this->_request->get('num',-1)-0, $this->userOrAccountId);
			$this->_view->assign('userIdentifier',$this->user->getAccountId());
			$this->_view->assign('now',date('Ymd H:i:s'));
		}
	}
}
