<?php
class Monkey_CustomerTier_Model_Attribute_Tiersource extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    protected $_options = null;

    public function getAllOptions(){
       
       $collection = Mage::getModel('customertier/customertier')
           ->getCollection();
       $collection->addFieldToSelect(array('id','tier_name'));
       $collection->load();
       $result[] = array(
               'value' => 0,
               'label' =>'None'
           );
       foreach($collection as $tier)
       {
           $result[] = array(
               'value' => $tier->getId(),
               'label' => $tier->getTierName()
           );
       }
       return $result;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions();

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }
 
}