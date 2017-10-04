<?php

class Monkey_CustomerTier_Block_Adminhtml_Customertier_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype'=> 'multipart/form-data')
        );

        $form->setUseContainer(true);
        $this->setForm($form);
         $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Tier Details')
            )
        );


        $tierSingleton = Mage::getSingleton(
            'customertier/customertier'
        );
        $fieldset->addField('tier_name', 'text', array(
            'label'     => Mage::helper('customertier')->__('Tier Name'),
            'name'      => 'tier_name',
            'required'  => true,

        ));
        
        $fieldset->addField('tier_min_range', 'text', array(
            'label'     => Mage::helper('customertier')->__('Tier Min. Range'),
            'name'      => 'tier_min_range',          
            'required'  => true, 
            'class' =>'validate-compare  range'
        ));
        
        $fieldset->addField('tier_max_range', 'text', array(
            'label'     => Mage::helper('customertier')->__('Tier Max. Range'),
            'name'      => 'tier_max_range',           
            'required'  => true, 
            'class' =>'validate-compare range'
        ));

        $field = $fieldset->addField(
            'store_id',
            'multiselect',
            array(
                'name'     => 'stores[]',
                'label'    => Mage::helper('customertier')->__('Store Views'),
                'title'    => Mage::helper('customertier')->__('Store Views'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            )
        );
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);
        
        
        $fieldset->addField('order_status', 'select', array(
            'label'    => Mage::helper('customertier')->__('Order Status'),
            'name'     => 'order_status',
            'values'    => $tierSingleton->getOrderStatus(),
            'required'  => true

        ));
        
        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('customertier')->__('Status'),
            'name'     => 'status',
            'required'  => true,
            'values'    =>array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('customertier')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('customertier')->__('Disabled'),
                    ),
                )
        ));
        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('customertier')->__('Status'),
            'name'     => 'status',
            'required'  => true,
            'values'    =>array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('customertier')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('customertier')->__('Disabled'),
                    ),
                )
        ));
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('customertier_data')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $form->setValues(array('status'=>1));
        if (Mage::getSingleton("adminhtml/session")->getCustomertierData())
        {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getCustomertierData());
            Mage::getSingleton("adminhtml/session")->setCustomertierData(null);
        } 
        elseif(Mage::registry("customertier_data")) {
            $form->setValues(Mage::registry("customertier_data")->getData());
        }
 
        return parent::_prepareForm(); 
    }
  
}