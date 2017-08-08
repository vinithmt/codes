<?php
/*
* Monkey Sports Geolocation Store List
* Uses magestore store locator to get a list of all active stores
* /-----------------------------------------------------------------------------------/
*
* Mage::helper('monkey_geolocation/stores')->monkey_getListStoreJson();
*      Piggybacks off of magestore store locator to get a json list of all available stores
*      Only shows active stores in json
*/

// Standard Magento helper class
class Monkey_Geolocation_Helper_Stores extends Mage_Core_Block_Template {

    public function monkey_getListStoreJson() {
        $storeId = Mage::app()->getStore()->getStoreId();
        $collections = Mage::getModel('storelocator/storelocator')->getCollection()
            ->setStoreId($storeId)
            ->addFieldToSelect(array('name','phone','rewrite_request_path', 'country','address', 'state', 'latitude', 'longtitude', 'image_icon'))
            ->addFieldToFilter('status', 1);
        if (Mage::helper('storelocator')->getConfig('sort_store')) {
            $collections->setOrder('name', 'ASC');
        } else {
            $collections->setOrder('sort', 'DESC');
        }

        return json_decode( Mage::helper('core')->jsonEncode($collections->getData()) );
    }


}
