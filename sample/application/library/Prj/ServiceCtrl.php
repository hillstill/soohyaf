<?php
namespace Prj;
class ServiceCtrl  extends \Prj\BaseCtrl {


	public function init()
	{
		parent::init();
		$render = $this->ini->viewRenderType();
		if($render==='html'){
			$this->ini->viewRenderType('json');
		}
	}
}
