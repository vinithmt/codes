<?php
class Monkey_CustomerTier_Model_Customertier    extends Mage_Core_Model_Abstract
{
 
    protected function _construct()
    {
       
        $this->_init('customertier/customertier');
    } 

     
    public function getOrderStatus()
    {
       $statuses = Mage::getModel('sales/order_status')->getCollection()->toOptionArray(); 
       $array = array();
       foreach ( $statuses as $key => $value) {
           $array[$value['value']] = $value['label'];
       } 
       return $array;
    }
    protected function _beforeSave()
    {
        parent::_beforeSave(); 
        $this->_updateTimestamps();
        $this->_prepareUrlKey();
        
        return $this;
    }
    
    protected function _updateTimestamps()
    {
        $timestamp = now(); 
        $this->setUpdatedAt($timestamp);
        
        if ($this->isObjectNew()) {
            $this->setCreatedAt($timestamp);
        }
        
        return $this;
    }
    
    protected function _prepareUrlKey()
    { 
        return $this;
    }
}