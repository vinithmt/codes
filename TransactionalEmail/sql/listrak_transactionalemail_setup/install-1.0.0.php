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
try {

    $this->startSetup();

    $table = new Varien_Db_Ddl_Table();
    $table->setName($this->getTable('transactionalemail/attributes'));

    $table->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'auto_increment' => true,
            'unsigned'       => true,
            'nullable'       => false,
            'primary'        => true,
        )
    );
    $table->addColumn(
        'input_field',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
        )
    );
    $table->addColumn(
        'attribute_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
        )
    );

    $table->addColumn(
        'header_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
        )
    );
    $table->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
        )
    );
    $table->addColumn(
        'data_type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
        )
    );

    $table->addColumn(
        'max_length',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
        )
    );
      $table->addColumn(
        'transactional_message_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
        )
    )
    ->addForeignKey(
            $this->getFkName(
                'transactionalemail/attributes',
                'transactional_message_id',
                'transactionalemail/transactionalmessages',
                'id'
            ),
            'transactional_message_id',
            $this->getTable('transactionalemail/transactionalmessages'),
            'id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $table->setOption('type', 'InnoDB');
    $table->setOption('charset', 'utf8');
    $this->getConnection()->createTable($table);

    $table = new Varien_Db_Ddl_Table();
    $table->setName($this->getTable('transactionalemail/transactionalmessages'));

    $table->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'auto_increment' => true,
            'unsigned'       => true,
            'nullable'       => false,
            'primary'        => true,
        )
    );

    $table->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
        )
    );

    $table->addColumn(
        'transactional_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
        )
    );
    $table->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
        )
    )->addForeignKey(
            $this->getFkName(
                'transactionalemail/transactionalmessages',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id',
            $this->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
    ); 
    $table->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
    'nullable' => false,
    'default' => 1,
    ), 'Status');
        

    $table->setOption('type', 'InnoDB');
    $table->setOption('charset', 'utf8');
    $this->getConnection()->createTable($table);
 

    $table = new Varien_Db_Ddl_Table();

    $table->setName($this->getTable('transactionalemail/queue'));

    $table->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'auto_increment' => true,
            'unsigned'       => true,
            'nullable'       => false,
            'primary'        => true,
        )
    );
    $table->addColumn(
        'transactional_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        100,
        array(
            'nullable' => false,
        )
    );
    $table->addColumn(
        'api_request',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '64K',
        array(
            'nullable' => false,
        )
    );
    $table->addColumn(
        'api_response',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '64K',
        array(
            'nullable' => false,
        )
    ); 
    $table->addColumn(
        'send_status',
        Varien_Db_Ddl_Table::TYPE_CHAR,
        '40',
        array(
            'nullable' => false,
        )
    );
    $table->addColumn(
        'retries',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'unsigned' => true,
            'nullable' => false,
        )
    );
    $table->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            "default"  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
            'nullable' => false,
        )
    );
    $table->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(

            'nullable' => false,
        )
    );

    $table->setOption('type', 'InnoDB');
    $table->setOption('charset', 'utf8');
    $this->getConnection()->createTable($table); 
    $this->endSetup();
 
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'listrak_setup.log');
}