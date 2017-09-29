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
class Listrak_TransactionalEmail_Model_Api_Customer extends Varien_Object implements Listrak_TransactionalEmail_Model_Api_Extrafunction
{

    public function getValue()
    {
        switch ($this->getInputElement()) {
            case 'reseturl':

                return $this->_getResetPasswordUrl();
                break;

            case 'login_url':
                return $this->_getAccountUrl();
                break;

        }
    }

    private function _getResetPasswordUrl()
    {
        return Mage::getUrl(
            'customer/account/resetpassword',
            array('_query' => 'id=' . $this->getObjects()->getId() . '&token=' . $this->getObjects()->getRpToken())
        );

    }

    private function _getAccountUrl()
    {
        return Mage::getUrl('customer/account');

    }

}
