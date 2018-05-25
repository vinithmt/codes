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
            case 'custom_shipping_description':
                return $this->_getShippingDescription();
                break;

        }
    }

    private function _getItemsHtml()
    {

        $orderItemBlock = Mage::app()->getLayout()->createBlock('sales/order_email_items', 'items')
            ->setTemplate('email/order/items.phtml');
        $orderSubTotalBlock = Mage::app()->getLayout()->createBlock('sales/order_totals', 'order_totals')
            ->setTemplate('sales/order/totals.phtml');
        $orderTaxBlock = Mage::app()->getLayout()->createBlock('tax/sales_order_tax', 'tax')->setTemplate('tax/order/tax.phtml');
        $orderSubTotalBlock->setChild('tax', $orderTaxBlock);
        $orderItemBlock->setChild('order_totals', $orderSubTotalBlock);
        $html = $orderItemBlock->setOrder($this->getObjectOfInput())
            ->addItemRender(
                'default',
                'sales/order_email_items_order_default',
                'email/order/items/order/default.phtml'
            )->toHtml();

        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        return $html;

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

        if (!$this->getObjectOfInput()->getShippingAddress()) {

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

    }

    private function _getShippingData($element)
    {

        if (!$this->getObjectOfInput()->getShippingAddress()) {
            return $this->getObjectOfInput()->getBillingAddress()->getData(str_replace('shipping_', '', $element));

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

        if (!$this->getObjectOfInput()->getShippingAddress()) {

            return $this->getObjectOfInput()->getBillingAddress()->getCountryModel()->getName();
        } else {
            return $this->getObjectOfInput()->getShippingAddress()->getCountryModel()->getName();
        }

    }

    private function _getShipmentTrackingHTML()
    {
        $_shipment   = $this->getObjects()['shipment'];
        $_order      = $this->getObjects()['order'];
        $trackingUrl = '';
        $returnResult = '';
        foreach ($_shipment->getAllTracks() as $_item) {
            //Assuming we are only dealing with ups and usps. #1877
           if (strtolower($_item->getCarrierCode()) =='usps') {
                $trackingUrl = ' <a href="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '">' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '</a> ';
            } elseif (strtolower($_item->getCarrierCode()) == 'ups') {
                $trackingUrl = '<a href="http://www.apps.ups.com/WebTracking/track?track=yes&trackNums=' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '">' . Mage::helper('core')->escapeHtml($_item->getNumber()) . '</a>';
            } else {
                $trackingUrl = Mage::helper('core')->escapeHtml($_item->getNumber());
            }
             $returnResult .= '<div style="float:left"> '.Mage::helper('core')->escapeHtml($_item->getTitle()).' : '.$trackingUrl.'</div><br>';
        }
        return $returnResult;
    }

    private function _getShippingDescription()
    {

        if (!$this->getObjectOfInput()->getShippingDescription()) {
            return 'None';
        }

        return $this->getObjectOfInput()->getShippingDescription();

    }

}
