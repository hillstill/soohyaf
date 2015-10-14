<?php
/**
 * 错误处理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ErrorController extends \Yaf_Controller_Abstract {
	public function errorAction($exception) {
		$ini = \Sooh\Base\Ini::getInstance();
		$render = $ini->viewRenderType();
		switch ($render){
			case "json":
				header('Content-type: application/json');
				break;
			case 'jsonp':
				break;
			default:
				$ini->viewRenderType('json');
				header('Content-type: application/json');
				break;
		}
		if(is_a($exception, '\Sooh\DB\Acl\ErrNeedsLogin')){
			\Sooh\Base\Log\Data::getInstance()->ret = 'needs login';
//		}elseif(is_a($exception,'\Prj\ErrCode')){
//			$this->_view->assign('code',$exception->getCode());
//			$this->_view->assign('msg',$exception->getMessage());
//			\Sooh\Base\Log\Data::getInstance()->ret = $exception->getMessage();
//		}elseif(is_a($exception,'\Prj\RetCode')){
//			$this->_view->assign('code',200);
//			$this->_view->assign('msg',$exception->getMessage());
		}else{
			var_log($exception);
			$this->_view->assign('code',$exception->getCode());
			$this->_view->assign('msg',$exception->getMessage());
			\Sooh\Base\Log\Data::getInstance()->ret = $exception->getMessage();
		}
     }
}
