<?php
/**
 * Listrak Transactional Email Magento Extension
 *
 * @package    Listrak
 * @package    Listrak_TransactionalEmail
 * @author     MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright  2017 MonkeySports Inc
 * #1877
 */
class Listrak_TransactionalEmail_Model_Api_Abstract
{
    private $_apiUsername;
    private $_apiPassword;
    private $_apiUrl;
    private $_apiHeader;
    protected $_transactionalParams;
    protected $_listId;
    protected $_senderList;
    protected $_emailPostedData;
    protected $_queueId = null;
    protected $_retries = null;

    const LOG_FILE            = 'listrak_transactionalemail.log';
    const SEND_STATUS_FAILED  = 'Failed';
    const SEND_STATUS_SUCCESS = 'Success';

    /**
     * { function_description }
     */
    public function __construct()
    {
        $this->_apiUsername = Mage::getStoreConfig('remarketing/listrakapi/listrakApiUsername');
        $this->_apiPassword = Mage::getStoreConfig('remarketing/listrakapi/listrakApiPassword');
        $this->_apiHeader   = Mage::getStoreConfig('remarketing/listrakapi/listrakApiHeader');
        $this->_apiUrl      = Mage::getStoreConfig('remarketing/listrakapi/listrakApiUrl');
        $this->_setListId();

    }

    /**
     * Soap client login
     *
     * @return     SoapClient  Returns the soap object
     */
    protected function _accessApi($storeId=NULL)
    {       
        if($storeId != NULL)
        {
            $this->_apiUsername = Mage::getStoreConfig('remarketing/listrakapi/listrakApiUsername', $storeId);
            $this->_apiPassword = Mage::getStoreConfig('remarketing/listrakapi/listrakApiPassword', $storeId);
            $this->_apiHeader   = Mage::getStoreConfig('remarketing/listrakapi/listrakApiHeader', $storeId);
            $this->_apiUrl      = Mage::getStoreConfig('remarketing/listrakapi/listrakApiUrl', $storeId);
        }

        $creds = array(
            'UserName' => $this->_apiUsername,
            'Password' => $this->_apiPassword,
        );
        $authvalues = new SoapVar($creds, SOAP_ENC_OBJECT);
        $headers[]  = new SoapHeader($this->_apiHeader, 'WSUser', $creds);
        $soapClient = new SoapClient($this->_apiUrl,
            array('trace' => 1, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2)
        );

        $soapClient->__setSoapHeaders($headers);

        return $soapClient;

    }

    /**
     * Sets the list identifier of listrak
     */
    private function _setListId()
    { 
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $storeId       = Mage::app()->getRequest()->getParam('store_id');
            $this->_listId = Mage::getStoreConfig('remarketing/listraklistids/listrakListId', $storeId); 
        } else {
            $this->_listId = Mage::getStoreConfig('remarketing/listraklistids/listrakListId');
        }
    }

    /**
     * map input field with attribute assign to it and assign the value of input
     *
     */
    private function _mapValuesWithAttributes()
    {
    
        $extraFnclassName = '';
        if ($extraFnclassName = $this->_getExtraFunctionModelName()) {
            $extraFnModel = Mage::getModel("transactionalemail/api_" . $extraFnclassName);
        }
        $mapValuesArray = array();
        $model          = Mage::getModel("transactionalemail/transactionalmessages")->getCollection()
            ->addFieldToFilter('name', $this->_emailPostedData['code'])
            ->addFieldToFilter('store_id', $this->_emailPostedData['storeId']);
        if ($model->getSize() > 0) {
            $tmData                                           = $model->getFirstItem()->getData();
            $this->_transactionalParams['TransactionalMsgID'] = $tmData['transactional_id'];
            $attributeModel                                   = Mage::getModel("transactionalemail/attributes")->getCollection()
                ->addFieldToFilter('transactional_message_id', $tmData['id'])
                ->getItems();
            foreach ($attributeModel as $key => $item) {
                $fieldValue = '';
                $array      = $this->_getInputFieldNClassObject($item->getInputField());
              
                if (is_object($this->_emailPostedData['templateParams'][$array['object']])) { 
                    $fieldValue = $this->_emailPostedData['templateParams'][$array['object']]->getData($array['input']);
                }
                elseif (is_array($this->_emailPostedData['templateParams'])) { 
                    $fieldValue = $this->_emailPostedData['templateParams'][$array['input']];
                }

                if ($extraFnclassName && $fieldValue == "") {
                    $extraFnModel->setObjects($this->_emailPostedData['templateParams']);
                    $extraFnModel->setInputElement($array['input']);
                     $extraFnModel->setObjectOfInput($this->_emailPostedData['templateParams'][$array['object']]);
                    $fieldValue = $extraFnModel->getValue();
                }
                $mapValuesArray[] = array('AttributeID' => $item->getAttributeId(), 'Value' => $fieldValue);
            }
        }
        $this->_transactionalParams['ProfileData']['WSProfileAttributeValue'] = $mapValuesArray;

    }
    /**
     * Generates the params for the listrak api.
     *
     */
    protected function _generateParams()
    { 
        $this->_transactionalParams = array('EmailAddress' =>$this->_emailPostedData['emailInfos'], 'storeId'=>$this->_emailPostedData['storeId']);
        $this->_mapValuesWithAttributes(); 
    }

    /**
     * Gets the input field n class object.
     *
     * @param      <type>  $element  The element
     *
     * @return     array   The input field n class object.
     */
    private function _getInputFieldNClassObject($element)
    {
        $array = array();
        $array = explode('/', $element);
        if(count($array) > 1)
            $array = array('object' => $array[0], 'input' => $array[1]);
        else
           $array = array('input' => $array[0]);   
        return $array;
    }

    /**
     * Sends a transactional message.
     */
    protected function _sendTransactionalMessage($storeId=NULL)
    { 
    
        if ($this->_queueId == null) {
            $this->_generateParams();
            $storeId =$this->_emailPostedData['storeId'];
        } 


        try {
            $soapClient = $this->_accessApi($storeId);  
            $rest       = $soapClient->SendTransactionalMessage($this->_transactionalParams);
            switch ($rest->SendTransactionalMessageResult) {
                case 'FailedInvalidEmailAddress':
                    $this->_addToQueue(self::SEND_STATUS_FAILED, $rest->SendTransactionalMessageResult);
                    break;
                case 'Success':
                    $this->_addToQueue(self::SEND_STATUS_SUCCESS, $rest->SendTransactionalMessageResult);
                    break;
            }
        } catch (SoapFault $e) {
            $this->_addToQueue(self::SEND_STATUS_FAILED, $e->getMessage());
            Mage::log($e->getMessage(), null, self::LOG_FILE);
        }

    }

    /**
     * Adds to queue.
     *
     * @param      <string>  $sendStatus   The send status
     * @param      <array>  $apiResponse  The api response
     */
    protected function _addToQueue($sendStatus, $apiResponse)
    {
        $model = Mage::getModel('transactionalemail/queue');
        if ($this->_queueId == null) {
            $model->setApiRequest(json_encode($this->_transactionalParams));
            $model->setTransactionalCode($this->_emailPostedData['code']);
        } else {

            $model->setId($this->_queueId);
            $model->setRetries($this->_retries + 1);
            $model->setUpdatedAt(now());
        }
        $model->setApiResponse($apiResponse);
        $model->setSendStatus($sendStatus);
        $model->save();
    }

    /**
     * Determines if its in sender list.
     *
     * @param      <string>   $list   The list
     *
     * @return     boolean  True if in sender list, False otherwise.
     */
    public function isInSenderList($list, $storeId)
    { 
        if (in_array($list, array_keys($this->_senderList))) { 
            $model = Mage::getModel("transactionalemail/transactionalmessages")->getCollection()
                ->addFieldToFilter('name', $this->_senderList[$list]['code'])
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('is_active', 1);
            if ($model->getSize() > 0) {
                 
                if (isset($this->_senderList[$list]['email-class'])) {
 
                    $this->_emailPostedData['email-class'] = $this->_senderList[$list]['email-class'];
                }
                
                return $this->_senderList[$list]['code'];
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

    private function _getExtraFunctionModelName()
    {
        foreach ($this->_senderList as $key => $value) {

            if ($value['code'] == $this->_emailPostedData['code']) {

                if ($value['email-class']) {
                    return $value['email-class'];
                } else {
                    return null;
                }

                break;
            }

        }
    }

}
