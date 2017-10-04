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
class Listrak_TransactionalEmail_Model_Api extends Listrak_TransactionalEmail_Model_Api_Abstract
{
    // How to know the which objects are passed?
    //  app\code\community\Listrak\TransactionalEmail\Model\Email\Template\Mailer.php send()
    //   PrintR the parameter templateParams   for example the output will
    //   be templateParams['order'] to get the input or post values order->getData();
    //   Th formart for senderlist must be
    //   1) templateParams objects in comma seperated
    //   2) transactional code
    //   3) if any custom class to call and append the value to the params.

    protected $_senderList = array(
        'customer,back_url'                            => array('code' => 'welcome_email', 'email-class' => 'customer'),
        'customer'                                     => array('code' => 'password_forgot', 'email-class' => 'customer'),
        'order,shipment,comment,billing,payment_html'  => array('code' => 'order_shipment', 'email-class' => 'order'),
        'order,billing,payment_html'                   => array('code' => 'order_confirmation', 'email-class' => 'order'),
        'order,comment,billing'                        => array('code' => 'order_cancel', 'email-class' => 'order'),
        'monkeysports_rewards_earning_notification'    => array('code' => 'rewards_earning_notification'),
        'monkeysports_rewards_expiration_notification' => array('code' => 'rewards_expiration_notification')
    );

    public function getAttributes()
    {

        $soapClient = $this->_accessApi();
        if ($this->_listId == '') {
            throw new Exception('List id not found in this store. Please configure in the System > Config > Customer > Listrak > Api.');
        }

        $params = array(
            'ListID' => $this->_listId,
        );
        $results =  '';
        $results = $soapClient->GetProfileHeaderCollection($params);
       
        if (isset($results->WSException) && !is_null($results->WSException)) {
            if (isset($results->WSException->Description)) {
                throw new Exception($results->WSException->Description);
            }
        }
        $attributes = $results->GetProfileHeaderCollectionResult->WSProfileHeader;
        return $attributes;

    }

    public function sendMail($code, $sender, $emailInfos, $templateParams, $storeId)
    {
        $emailInfo                  = array_pop($emailInfos); 
        $this->_emailPostedData['code']           = $code;
        $this->_emailPostedData['sender']         = $sender;
        $this->_emailPostedData['emailInfos']     =  $emailInfo->getToEmails()[0];
        $this->_emailPostedData['templateParams'] = $templateParams;
        $this->_emailPostedData['storeId']        = $storeId;

        $this->_sendTransactionalMessage();
        // while (!empty($this->_emailPostedData['emailInfos'])) {
        //     // Send all emails from corresponding list
        //     // To do
        // }

    }

    public function resendMail($queueData)
    {
        $this->_transactionalParams     = json_decode($queueData->getApiRequest(), true);
        $this->_emailPostedData['code'] = $queueData->getTransactionalCode();
        $this->_queueId                 = $queueData->getId();
        $this->_retries                 = $queueData->getRetries();
        $this->_sendTransactionalMessage();
    }

    public function sendSweetToothEmail($customer, $template, $vars = null)
    {    
       
        if( $code = Mage::getModel('transactionalemail/api')->isInSenderList($template, $customer->getStoreId()) ) {  
           
            $sender = array(
                'name'  => strip_tags(Mage::helper("stmonkeysports/config")->getEmailSenderName($customer->getStoreId())),
                'email' => strip_tags(Mage::helper("stmonkeysports/config")->getEmailSenderEmail($customer->getStoreId()))
            );
            $this->_emailPostedData['code']           = $code;
            $this->_emailPostedData['sender']         = $sender;
            $this->_emailPostedData['emailInfos']     = $customer->getEmail();
            $this->_emailPostedData['templateParams'] = $vars;
            $this->_emailPostedData['storeId']        = $customer->getStoreId();
            $this->_sendTransactionalMessage();
              return true;

        }else{
 
             /* @var $translate Mage_Core_Model_Translate */
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            /* @var $email Mage_Core_Model_Email_Template */
            $email = Mage::getIsDeveloperMode() ? Mage::getModel('stmonkeysports/email_template') : Mage::getModel('core/email_template');

            $sender = array(
                'name'  => strip_tags(Mage::helper("stmonkeysports/config")->getEmailSenderName($customer->getStoreId())),
                'email' => strip_tags(Mage::helper("stmonkeysports/config")->getEmailSenderEmail($customer->getStoreId()))
            );

            $email->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $customer->getStoreId())
            );

            $email->sendTransactional($template, $sender, $customer->getEmail(), $customer->getName(), $vars);
            $translate->setTranslateInline(true);

            return $email->getSentSuccess(); 


        }

        
      
    }

}
