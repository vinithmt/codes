<?php
class Monkey_CustomerTier_Model_Resource_Customertier_Collection    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct(); 
        $this->_init( 'customertier/customertier');
        $this->_map['fields']['store'] = 'tier_store.store_id';
    }
     public function addGroupByCustomerFilter()
    {
        $this->getSelect()->group('main_table.customer_id');
        return $this;
    }
    
    /**
     * Adds a store filter for Admin Gird layout
     *
     * @param      array    $store      The store
     * @param      boolean  $withAdmin  The with admin
     *
     * @return     object   Data objects
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!isset($this->_joinedFields['store'])) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            if (!is_array($store)) {
                $store = array($store);
            }
            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }
            $this->addFilter('store', array('in' => $store), 'public');
            $this->_joinedFields['store'] = true;
        }
        return $this;
    }

    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('tier_store' => $this->getTable('customertier/customertier_store')),
                'main_table.id = tier_store.tier_id',
                array()
            )
            ->group('main_table.id');
            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }
}