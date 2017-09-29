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
class Listrak_TransactionalEmail_Adminhtml_TransactionalqueueController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('remarketing')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__("Manage Email Queue"), Mage::helper('adminhtml')->__("Manage Email Queue"));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    { 
        $this->_title($this->__("Manage Email Queue"));

        $this->_initAction()->renderLayout();
    }

    

}
