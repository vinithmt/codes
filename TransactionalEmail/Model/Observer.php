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
class Listrak_TransactionalEmail_Model_Observer
{

    private $pageSize = 1000;

    /**
     * Process the queue of failed transactional emails and retires untill it succeed
     *
     */
    public function processQueue()
    {

        $apiModel   = Mage::getModel('transactionalemail/api');
        $queueModel = Mage::getModel('transactionalemail/queue')->getCollection()
            ->addFieldToFilter('send_status', $apiModel::SEND_STATUS_FAILED)
            ->setPageSize($this->pageSize);
        $pages = $queueModel->getLastPageNumber();
        $sql   = $queueModel->getSelect();

        for ($curPage = 1; $curPage <= $pages; $curPage++) {

            $queueModel->setCurPage($curPage);
            $queueModel->load();
            if ($queueModel->count() == 0) {
                break 1;
            }
            foreach ($queueModel as $key => $value) {
                $apiModel->resendMail($value);
            }
            $queueModel->clear();
        }

    }

    /**
     * Erase the records of success transactional email of 30 days.
     *
     */
    public function processQueueErase()
    {

        $queueModel = Mage::getModel('transactionalemail/queue')->eraseRecords();

    }
}
