<?php
/**
 * Listrak Transactional Email Magento Extension
 *
 * @category  Listrak
 * @package   Listrak_TransactionalEmail
 * @author    MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright 2017 MonkeySports, Inc
 * #1877
 */
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalmessages_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'transactionalemail';
        $this->_controller = 'adminhtml_transactionalmessages';

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete'));
        $this->_addButton('save_edit', array(
            'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit(\'' . $this->getSaveAndContinueUrl() . '\');',
            'class'   => 'save',
        ), 1);

        $newOrEdit = $this->getRequest()->getParam('id')
        ? $this->__('Edit')
        : $this->__('New');
        $this->_headerText = $newOrEdit . ' ' . $this->__('Transactional Message');

    }

    private function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            'back'           => 'edit',
            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
        ));
    }
}
