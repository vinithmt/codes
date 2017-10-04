<?php

class Monkey_CustomerTier_Block_Adminhtml_Customertier_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'customertier';
        $this->_controller = 'adminhtml_customertier';

         $this->_updateButton('save', 'label', Mage::helper('customertier')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('customertier')->__('Delete'));
        $this->_addButton('save_edit', array(
            'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit(\'' . $this->getSaveAndContinueUrl() . '\');',
            'class'   => 'save',
        ), 1); 
		 

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText =  $newOrEdit . ' ' . $this->__('Customer Tier');
    }

   

    /**
     * Save and continue url
     *
     * @return string
     */
    private function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            'back'           => 'edit',
            $this->_objectId => $this->getRequest()->getParam($this->_objectId)
        ));
    }
}