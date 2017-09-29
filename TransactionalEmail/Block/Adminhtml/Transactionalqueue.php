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
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalqueue extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Grid container
        $this->_controller = 'adminhtml_transactionalqueue';
        $this->_blockGroup = 'transactionalemail';
        $this->_headerText = Mage::helper('adminhtml')->__("Manage Email Queue");

        parent::__construct();
    }
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return parent::_prepareLayout();
    }

}
