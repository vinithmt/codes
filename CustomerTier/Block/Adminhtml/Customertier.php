<?php

class Monkey_CustomerTier_Block_Adminhtml_Customertier extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Grid container
        $this->_controller = 'adminhtml_customertier';
        $this->_blockGroup = 'customertier';
        $this->_headerText = Mage::helper('customertier')->__("Customer Tier");
        $this->_addButtonLabel = Mage::helper('customertier')->__('Add New Customer Tier');

        parent::__construct();
    }
     
	
}