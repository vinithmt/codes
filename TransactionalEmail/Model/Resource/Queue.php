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
class Listrak_TransactionalEmail_Model_Resource_Queue extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('transactionalemail/queue', 'id');
        $this->_write = $this->_getWriteAdapter();
    }

    public function eraseRecords()
    {
        $apiModel   = Mage::getModel('transactionalemail/api');
        $conditions =  'created_at >=  NOW() - INTERVAL 30 DAY';
        $conditions .= $this->_write->quoteInto(' AND send_status  = ?', $apiModel::SEND_STATUS_SUCCESS);
        $this->_write->delete(
            $this->getTable("transactionalemail/queue"),
            $conditions
        );
    }
}
