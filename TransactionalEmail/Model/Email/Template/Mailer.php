<?php
/**
 * Overriden Email Template Mailer Model  To hook the api
 * Listrak Transactional Email Magento Extension
 *
 * @category  Listrak
 * @package   Listrak_TransactionalEmail
 * @author    MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright 2017 MonkeySports Inc
 * #1877
 */
class Listrak_TransactionalEmail_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{

    /**
     * Send all emails from email list
     * @see self::$_emailInfos
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function send()
    {
        //[0] => order [1] => shipment [2] => comment [3] => billing [4] => payment_html 
      //    Mage::log(array_keys($this->getTemplateParams()), null, 'listrak_transactionalemail.log');
    
 
        $list = implode(',', array_keys($this->getTemplateParams()));
        if ($code = Mage::getModel('transactionalemail/api')->isInSenderList($list, $this->getStoreId())) { 
            try {
                Mage::getModel('transactionalemail/api')
                    ->sendMail(
                        $code,
                        $this->getSender(),
                        $this->_emailInfos,
                        $this->getTemplateParams(),
                        $this->getStoreId());

            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'listrak_transactionalemail.log');

            }

        } else {
 
            /** @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate = Mage::getModel('core/email_template');
            // Send all emails from corresponding list
            while (!empty($this->_emailInfos)) {
                $emailInfo = array_pop($this->_emailInfos);
                // Handle "Bcc" recipients of the current email
                $emailTemplate->addBcc($emailInfo->getBccEmails());
                // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
                $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                    ->setQueue($this->getQueue())
                    ->sendTransactional(
                        $this->getTemplateId(),
                        $this->getSender(),
                        $emailInfo->getToEmails(),
                        $emailInfo->getToNames(),
                        $this->getTemplateParams(),
                        $this->getStoreId()
                    );
            }
        }
        return $this;
    }

}
