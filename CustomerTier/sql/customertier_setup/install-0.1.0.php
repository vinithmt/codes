<?php 
$this->startSetup(); 
$table = new Varien_Db_Ddl_Table();  
$table->setName($this->getTable('customertier/customertier')); 

$table->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);

$table->addColumn(
    'tier_name',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable' => false,
    )
);
$table->addColumn(
    'tier_min_range',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable' => false,
         'unsigned'  => true,
    )
);
$table->addColumn(
    'tier_max_range',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    11,
    array(
        'nullable' => false,
         'unsigned'  => true,
    )
); 
$table->addColumn(
    'order_status',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    15,
    array(  
        'nullable'=> false, 
    )
);
$table->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
    'nullable' => false,
    'default' => 1,
));
$table->addColumn(
    'created_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => false,
    )
);
$table->addColumn(
    'updated_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => false,
    )
);
 
$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');  
$this->getConnection()->createTable($table);

$table = $table = new Varien_Db_Ddl_Table();  
$table->setName($this->getTable('customertier/customertier_store')); 

    $table->addColumn(
        'tier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable'  => false, 
             'unsigned'  => true,
        ),
        'Tier ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'unsigned'  => true,
            'nullable'  => false, 
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'customertier/customertier_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'customertier/customertier_store',
            'tier_id',
            'customertier/customertier',
            'id'
        ),
        'tier_id',
        $this->getTable('customertier/customertier'),
        'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'customertier/customertier_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Tier & store relation table');
$this->getConnection()->createTable($table);



$this->endSetup();