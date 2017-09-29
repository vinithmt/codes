<?php
/**
 * Listrak Transactional Email Magento Extension
 *
 * @category  Listrak
 * @package   Listrak_TransactionalEmail
 * @author    MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright 2017 MonkeySports, Inc
 */
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalqueue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('transactionalqueueGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('transactionalemail/queue')->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns to be shown
     *
     * @return $this|void
     */
    protected function _prepareColumns()
    {

        parent::_prepareColumns();
        $this->addColumn('', array(
            'header'   => Mage::helper('adminhtml')->__('ID'),
            'index'    => 'id',
            'type'     => 'int',
            'align'    => 'left',
            'width'    => '10%',
            'sortable' => false,
        ));

        $this->addColumn('transactional_code', array(
            'header'   => Mage::helper('adminhtml')->__('Name'),
            'index'    => 'transactional_code',
            'type'     => 'text',
            'align'    => 'left',
            'width'    => '20%',
            'sortable' => false,
        ));
        $this->addColumn('send_status', array(
            'header'   => Mage::helper('adminhtml')->__('Send Status'),
            'index'    => 'send_status',
            'type'     => 'text',
            'align'    => 'left',
            'width'    => '16%',
            'sortable' => false,
        ));
        $this->addColumn('retries', array(
            'header'   => Mage::helper('adminhtml')->__('Retries'),
            'index'    => 'retries',
            'type'     => 'int',
            'align'    => 'left',
            'width'    => '16%',
            'sortable' => false,
        ));
        $this->addColumn('created_at', array(
            'header'   => Mage::helper('adminhtml')->__('Created At'),
            'index'    => 'created_at',
            'type'     => 'text',
            'align'    => 'left',
            'width'    => '16%',
            'sortable' => false,
        ));
        $this->addColumn('updated_at', array(
            'header'   => Mage::helper('adminhtml')->__('Updated At'),
            'index'    => 'updated_at',
            'type'     => 'text',
            'align'    => 'left',
            'width'    => '16%',
            'sortable' => false,
        ));

        return parent::_prepareColumns();
    }

    // /**
    //  * Set column for mass delete
    //  *
    //  * @return $this|Mage_Adminhtml_Block_Widget_Grid
    //  */
    // public function _prepareMassaction()
    // {
    //     $this->setMassactionIdField('id');
    //     $this->getMassactionBlock()->setFormFieldName('id');
    //     $this->getMassactionBlock()->addItem('run', array(
    //         'label' => Mage::helper('adminhtml')->__('Run now'),
    //         'url'   => $this->getUrl('*/*/runNow'),
    //     ));
        
    //     $this->getMassactionBlock()->addItem('delete', array(
    //         'label'   => Mage::helper('adminhtml')->__('Delete'),
    //         'url'     => $this->getUrl('*/*/massDelete', array('' => '')),
    //         'confirm' => Mage::helper('adminhtml')->__('Are you sure you want to delete?'),
    //     ));

    //     return $this;
    // }

    

}
