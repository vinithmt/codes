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
class Listrak_TransactionalEmail_Model_Resource_Attributes extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('transactionalemail/attributes', 'id');
        $this->_write = $this->_getWriteAdapter();
    }

    /**
     * Delete all attributes fields associated with a transactional Message Id
     *
     * @param      <int>  $transactionalMessageId  The transactional message identifier
     *
     * @return     void
     */
    public function deleteAttributeFields($transactionalMessageId)
    {

        $this->_write->delete(
            $this->getTable("transactionalemail/attributes"),
            $this->_write->quoteInto('transactional_message_id = ?', $transactionalMessageId)
        );
    }

}
