<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->modifyColumn(
    $this->getTable('customertier/customertier'),
    'tier_min_range', 
    array(
         'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '12,2',
        'nullable' => false,
         'unsigned'  => true,
    )
)->modifyColumn(
    $this->getTable('customertier/customertier'),
    'tier_max_range', 
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '12,2',
        'nullable' => false,
         'unsigned'  => true,
    )
); 

$installer->endSetup();