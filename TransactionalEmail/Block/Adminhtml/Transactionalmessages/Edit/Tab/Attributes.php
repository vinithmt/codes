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

class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalmessages_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        //  $vendorStore = Mage::registry('vendor_store_details');// new registry for different module
        $form = new Varien_Data_Form();
        //$form->setFieldNameSuffix('vendor_store');

        $fieldset = $form->addFieldset('transactionalmessage_attribute_form', array(
            'legend' => Mage::helper('adminhtml')->__('Map fields with attributes'),
        ));

        $multipleInput = $fieldset->addField('multiple_input', 'text', array(
            'name'  => 'Attributes',
            'label' => Mage::helper('adminhtml')->__('Attributes'),

        ));
        
        $multipleInput = $form->getElement('multiple_input');
        $multipleInput->setRenderer(
            $this->getLayout()->createBlock('transactionalemail/adminhtml_render_multipletextbox')
        );

        $this->setForm($form);
        // $form->addValues($vendorStore->getData());
        return parent::_prepareForm();

    }

}
