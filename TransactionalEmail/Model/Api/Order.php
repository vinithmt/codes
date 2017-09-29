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
class Listrak_TransactionalEmail_Model_Api_Order extends Varien_Object implements Listrak_TransactionalEmail_Model_Api_Extrafunction
{

    public function getValue()
    {
        switch ($this->getInputElement()) {
            case 'cartcontent':
                return $this->_getItemsHtml();
                break;
            case 'billing_firstname':
            case 'billing_lastname':
            case 'billing_city':
            case 'billing_region':
            case 'billing_telephone':
            case 'billing_postcode':
                return $this->_getBillingData($this->getInputElement());
                break;
            case 'shipping_firstname':
            case 'shipping_lastname':
            case 'shipping_city':
            case 'shipping_region':
            case 'shipping_telephone':
            case 'shipping_postcode':
                return $this->_getShippingData($this->getInputElement());
                break;
            case 'shipping_street_2':
                return $this->_getShippingStreet('street2');
                break;
            case 'shipping_street':
                return $this->_getShippingStreet('street');
                break;
            case 'billing_street_2':
                return $this->_getBillingStreet('street2');
                break;
            case 'billing_street':
                return $this->_getBillingStreet('street');
                break;
            case 'payment':
                return $this->_getPayment();
                break;
            case 'ordered_date':
                return $this->_getOrderedDate();
                break;
            case 'comment':
                return $this->_getOrderComment();
                break;
            case 'billing_country':
                return $this->_getBillingCountry();
                break;
            case 'shipping_country':
                return $this->_getShippingCountry();
                break;

            case 'login_url':
                //$this->getObjectOfInput()->getStoreId() assuming this order object
                return Mage::app()->getStore($this->getObjectOfInput()->getStoreId())->getUrl('customer/account/');
                break;
            case 'tracking_content':
                return $this->_getShipmentTrackingHTML();
                break;
        }
    }

    private function _getItemsHtml()
    {
        $layout = Mage::app()->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->addHandle('sales_email_order_items');

        $update->load();

        $layout->generateXml();
        $layout->generateBlocks();
        $layout->getBlock('root')->setTemplate('page/1column.phtml');

        //set block from layout handle <block type="sales/order_email_items" name="items" template="email/order/items.phtml">
        $layout->getBlock('content')->setChild('items', $layout->getBlock('items'));

        $layout->getBlock('content')->append('items');
        $block = $layout->getBlock('items')->setData('order', $this->getObjectOfInput());

        return $block->toHtml();
    }

    private function _getBillingStreet($element)
    {

        if ($element == 'street') {
            return $this->getObjectOfInput()->getBillingAddress()->getStreet()[0];
        } else {
            if (count($this->getObjectOfInput()->getBillingAddress()->getStreet()) > 1) {
                return $this->getObjectOfInput()->getBillingAddress()->getStreet()[1];
            }

        }

    }
    private function _getShippingStreet($element)
    {

        if (!$this->getObjectOfInput()->getShippingAddress()->getFormated('html')) {

            if ($element == 'street') {
                return $this->getObjectOfInput()->getBillingAddress()->getStreet()[0];
            } else {
                if (count($this->getObjectOfInput()->getBillingAddress()->getStreet()) > 1) {
                    return $this->getObjectOfInput()->getBillingAddress()->getStreet()[0];
                }

            }
        } else {
            if ($element == 'street') {
                return $this->getObjectOfInput()->getShippingAddress()->getStreet()[0];
            } else {
                if (count($this->getObjectOfInput()->getShippingAddress()->getStreet()) > 1) {
                    return $this->getObjectOfInput()->getShippingAddress()->getStreet()[0];
                }

            }
        }

    }

    private function _getBillingData($element)
    {
        return $this->getObjectOfInput()->getBillingAddress()->getData(str_replace('billing_', '', $element));
        // return $this->getObjectOfInput()->getBillingAddress()->getFormated('text');

    }

    private function _getShippingData($element)
    {
        if (!$this->getObjectOfInput()->getShippingAddress()->getFormated('html')) {
            return $this->getObjectOfInput()->getBillingAddress()->getData(str_replace('billing_', '', $element));
        } else {
            return $this->getObjectOfInput()->getShippingAddress()->getData(str_replace('shipping_', '', $element));
        }

    }

    private function _getPayment()
    {
        return $this->getObjectOfInput();
    }

    private function _getOrderedDate()
    {
        return $this->getObjectOfInput()->getCreatedAtFormated('long');
    }

    private function _getOrderComment()
    {
        $collection = Mage::getModel('sales/order_status_history')->getCollection()
            ->addFieldToFilter('status', array('null' => true))
            ->addFieldToFilter('entity_name', 'order')
            ->addFieldToFilter('is_customer_notified', 0)
            ->addFieldToFilter('parent_id', $this->getObjectOfInput()->getId())
            ->getFirstItem();

        return $collection->getComment();

    }

    private function _getBillingCountry()
    {
        return $this->getObjectOfInput()->getBillingAddress()->getCountryModel()->getName();
    }

    private function _getShippingCountry()
    {
        if (!$this->getObjectOfInput()->getShippingAddress()->getFormated('html')) {
            return $this->getObjectOfInput()->getBillingAddress()->getCountryModel()->getName();
        } else {
            return $this->getObjectOfInput()->getShippingAddress()->getCountryModel()->getName();
        }

    }

    private function _getShipmentTrackingHTML()
    {
        $_shipment = $this->getObjects()['shipment'];
        $_order    = $this->getObjects()['order'];
        $trackingUrl = '';
        foreach ($_shipment->getAllTracks() as $_item) {
            $configTrackingUrlPath = 'shipping/trackingurl/' . str_replace('productmatrix', 'premiumrate', $_order->getShippingMethod());
            //replace productmatrix to premiumrate because it apperas with this code when render shipping settings page
            $trackingUrl = Mage::getStoreConfig($configTrackingUrlPath);
            if ($trackingUrl){
                $trackingUrl = '<a href="' . $trackingUrl . Mage::helper('core')->escapeHtml($_item->getNumber()) . '">' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '</a>';
            }
            elseif ($this->getConfigData($_item->getCarrierCode())){
                $trackingUrl = '<a href="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '">' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '</a>';
            }
            elseif ($_item->getCarrierCode() == 'ups'){
                $trackingUrl = '<a href="http://www.apps.ups.com/WebTracking/track?track=yes&trackNums=' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '">' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '</a>';
            }
            else{
                $trackingUrl = Mage::helper('core')->escapeHtml($_item->getNumber());
            }
            
        } 
        return $trackingUrl;
    }

}
