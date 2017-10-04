<?php

class Monkey_CustomerTier_Block_Adminhtml_Customertier_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(); 
        $this->setId('Customertier');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('customertier/customertier')->getCollection();
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
            'header'   => Mage::helper('customertier')->__('ID'),
            'index'    => 'id',
            'type'     => 'int',
            'align'    => 'left',
            'width' => '10%', 
            'sortable' => true
        ));

        $this->addColumn('tier_name', array(
            'header'   => Mage::helper('customertier')->__('Tier Name'),
            'index'    => 'tier_name',
            'type'     => 'int',
            'align'    => 'left',
            'width' => '25%', 
            'sortable' => true
        ));
         $this->addColumn('tier_min_range', array(
            'header'   => Mage::helper('customertier')->__('Tier Min. Range'),
            'index'    => 'tier_min_range',
            'type'     => 'int',
            'align'    => 'left',
            'width' => '15%', 
            'sortable' => true
        ));
          $this->addColumn('tier_max_range', array(
            'header'   => Mage::helper('customertier')->__('Tier Max. Range'),
            'index'    => 'tier_max_range',
            'type'     => 'int',
            'align'    => 'left',
            'width' => '15%', 
            'sortable' => true
        ));
       if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('customertier')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'width' => '25%',
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('customertier')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'width' => '10%', 
                'options' =>  array(
                    '1' => Mage::helper('customertier')->__('Enabled'),
                    '0' => Mage::helper('customertier')->__('Disabled'),
                ),
                 'sortable' => true
            )
        );

        
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
            'label'=> Mage::helper('customertier')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('customertier')->__('Are you sure you want to delete?')
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

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}