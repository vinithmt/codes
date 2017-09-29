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
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalmessages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Grid container
        $this->_controller     = 'adminhtml_transactionalmessages';
        $this->_blockGroup     = 'transactionalemail';
        $this->_headerText     = Mage::helper('adminhtml')->__("Transactional Messages");
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Transactional Message');

        parent::__construct();
    }

}
