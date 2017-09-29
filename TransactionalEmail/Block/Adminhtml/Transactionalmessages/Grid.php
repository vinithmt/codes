<?php
/**
 * Listrak Transactional Email Magento Extension
 *
 * @category  Listrak
 * @package   Listrak_TransactionalEmail
 * @author    MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright 2017 MonkeySports, Inc
 */
class Listrak_TransactionalEmail_Block_Adminhtml_Transactionalmessages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('transactionalmessagesGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('transactionalemail/transactionalmessages')->getCollection();

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
            'sortable' => true,
        ));

        $this->addColumn('name', array(
            'header'   => Mage::helper('adminhtml')->__('Name'),
            'index'    => 'name',
            'type'     => 'int',
            'align'    => 'left',
            'width'    => '30%',
            'sortable' => true,
        ));
        $this->addColumn('transactional_id', array(
            'header'   => Mage::helper('adminhtml')->__('Transactional Id'),
            'index'    => 'transactional_id',
            'type'     => 'int',
            'align'    => 'left',
            'width'    => '30%',
            'sortable' => true,
        ));
        $this->addColumn('store_id', array(
            'header'          => 'Store',
            'index'           => 'store_id',
            'type'            => 'store',
            'width'           => '30%',
            'store_view'      => true,
            'display_deleted' => false,
            'renderer'        => 'Listrak_TransactionalEmail_Block_Adminhtml_Render_Store',
        ));
        $this->addColumn('is_active', array( 
            'header' => Mage::helper('adminhtml')->__('Status'), 
            'align' => 'left',
            'width' => '30%', 
            'index' => 'is_active',
             'type' => 'options', 
             'options' => array( 1 => 'Enabled', 2 => 'Disabled', ) 
        ));
       


        return parent::_prepareColumns();
    }

    /**
     * Set column for mass delete
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    public function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('adminhtml')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure you want to delete?'),
        ));
        $this->getMassactionBlock()->addItem('1', array(
            'label'   => Mage::helper('adminhtml')->__('Enable'),
            'url'     => $this->getUrl('*/*/massStatus', array('status' => '1')),
            
        ));
         $this->getMassactionBlock()->addItem('2', array(
            'label'   => Mage::helper('adminhtml')->__('Disable'),
            'url'     => $this->getUrl('*/*/massStatus', array('status' => '2')),
            
        ));

        return $this;
    }

    /**
     * Clicking the row, will take to the edit action
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
