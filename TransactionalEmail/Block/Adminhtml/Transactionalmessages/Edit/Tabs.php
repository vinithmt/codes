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
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalmessages_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('transactionalmessages_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__("Transactional Message"));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('transactional_message', array(
            'label'   => Mage::helper('adminhtml')->__('Transactional Message'),
            'title'   => Mage::helper('adminhtml')->__('Transactional Message'),
            'content' => $this->getLayout()->createBlock('transactionalemail/adminhtml_transactionalmessages_edit_tab_form')->toHtml(),
        ));
         $this->addTab('attribute_fields',array(
                'label'=>Mage::helper('adminhtml')->__('Attribute Fields'),
                'title'=>Mage::helper('adminhtml')->__('Attribute Fields'),
                'content'=>$this->getLayout()->createBlock('transactionalemail/adminhtml_transactionalmessages_edit_tab_attributes')->toHtml(),

         ));
        return parent::_beforeToHtml();
    }
}
