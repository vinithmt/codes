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
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalmessages_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form layout
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), '_secure' => true)),
            'method'  => 'post',
            'enctype' => 'multipart/form-data')
        );

        $this->setForm($form);
        $fieldset = $form->addFieldset('form_section', array('legend' => Mage::helper('adminhtml')->__("Transactional Message Detail")));

        $fieldset->addField('name', 'text', array(
            'label'              => Mage::helper('adminhtml')->__('Name/Code'),
            'name'               => 'name',
            'required'           => true,
            'after_element_html' => '<p class="nm"><small>This field is use to fetch this record according to each event such as order confirmation, change password etc. Recommeneded to use without whitespace for example orderconfirmation, changepassword.</small></p>',

        ));

        $fieldset->addField('transactional_id', 'text', array(
            'label'              => Mage::helper('adminhtml')->__('Transactional Id'),
            'name'               => 'transactional_id',
            'required'           => true,
            'after_element_html' => '<p class="nm"><small>The value must be the transactional id from LisTrak</small></p>',
        ));

        $field = $fieldset->addField(
            'store_id',
            'select',
            array(
                'name'     => 'store_id',
                'label'    => Mage::helper('adminhtml')->__('Store'),
                'title'    => Mage::helper('adminhtml')->__('Store'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            )
        );

        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'  => 'store_id',
                    'value' => Mage::app()->getStore(true)->getId(),
                )
            );
            Mage::registry('transactionalmessages_data')->setStoreId(Mage::app()->getStore(true)->getId());
        }
 

        $fieldset->addField('is_active', 'select', array(
            'label'    => Mage::helper('adminhtml')->__('Status'),
            'name'     => 'is_active',
            'required' => true,
            'values'   => array(1 => 'Enable', 2 => 'Disable') 

        ));
        $form->setValues(array('is_active'=>2));
        if (Mage::getSingleton("adminhtml/session")->getTransactionalMessagesData()) { 
            $form->setValues(Mage::getSingleton("adminhtml/session")->getTransactionalMessagesData());
            Mage::getSingleton("adminhtml/session")->setTransactionalMessagesData(null);
        } elseif (Mage::registry("transactionalmessages_data")->getData()) { 
            $form->setValues(Mage::registry("transactionalmessages_data")->getData());
        }
        return parent::_prepareForm();

    }
}
