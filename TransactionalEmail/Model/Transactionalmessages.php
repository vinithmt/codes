<?php
/**
 * Listrak Transactional Email Magento Extension
 *
 * @category  Listrak
 * @package   Listrak_TransactionalEmail
 * @author    MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright 2017 MonkeySports Inc
 * #1877
 */
class Listrak_TransactionalEmail_Model_Transactionalmessages extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
 
        $this->_init('transactionalemail/transactionalmessages');
    }

}
