<?php
$installer = new Mage_Customer_Model_Entity_Setup('core_setup');
$installer->startSetup();
$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute("customer", "customer_tier",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Customer Tier",
    "input"    => "select",
    "source"   => "customertier/attribute_tiersource",
    "visible"  => false,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => "Customer Tier", 
 ));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "customer_tier"); 
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'customer_tier',
    '111' 
);

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer"; 
$attribute->setData("used_in_forms", $used_in_forms)
        ->setData("is_used_for_customer_segment", true)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 0)
        ->setData("sort_order", 111)
        ;
$attribute->save(); 

$installer->endSetup();