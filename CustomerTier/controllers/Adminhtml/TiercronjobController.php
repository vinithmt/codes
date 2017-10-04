<?php

class Monkey_CustomerTier_Adminhtml_TiercronjobController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('customer');
		return $this;
	}   
	
    /**
     * Index action
     */
    public function indexAction()
    {
    	$this->_title($this->__("Customer Tier Cron Job"));
        $this->_initAction()->renderLayout();
        echo 1; exit;
    }

}