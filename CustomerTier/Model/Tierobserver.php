<?php
class Monkey_CustomerTier_Model_Tierobserver {

  private $tierData = array();
  private $orderPageSize = 100;  

  public function __construct(){ 
     $this->_getTierLevel(); 
  } 


  /**
   *  Process the tier with order
   *
   * @return     boolean  Return false if cron job running already else it will start new cron job session.
   */
  public function processTier()
  { 

    $this->_log('Before Memory Usage=>'.$this->_memoryUsage());
    $this->_getOrder();
    $this->_log('After Memory Usage=>'.$this->_memoryUsage());
   
  } 
 
  
  private function _getOrder()
  {   

    foreach ($this->tierData as $key => $tier) {
      $this->_log('Tier ID=>'. $tier->getId());  

      $orderModel = Mage::getModel('sales/order')->getCollection()
                              ->addAttributeToSelect('customer_id')  
                              ->addAttributeToFilter('main_table.status', array('in' => array($tier->getData('order_status')))) 
                              ->addAttributeToFilter('main_table.customer_id',  array('notnull' => true));  
      if(in_array(0, $tier->getStoreId()))
        $orderModel->addAttributeToFilter('main_table.store_id', array('nin' => $tier->getStoreId()));
      else
        $orderModel->addAttributeToFilter('main_table.store_id', array('in' => $tier->getStoreId()));  
    
      $orderModel->addExpressionFieldToSelect("total_amt", "sum({{grand_total}})", "grand_total"); 
      $orderModel->addExpressionFieldToSelect("gift_total_amt", "sum({{gift_cards_amount}})", "gift_cards_amount"); 
      $orderModel->setPageSize($this->orderPageSize);  
      $pages = $orderModel->getLastPageNumber();  
      $sql =  $orderModel->getSelect()->group(array('main_table.customer_id'))->having('total_amt >= ?', $tier->getTierMinRange());  
       $this->_log((string) $sql);   
         
      for($curPage=1; $curPage<=$pages; $curPage++){
          $this->_log('Records Count=>'.  $orderModel->count().'Page=>'. $pages);  
         
          $orderModel->setCurPage($curPage); 
          $orderModel->load(); 
          $i = 0;
          $j = 0; 
          if($orderModel->count() == 0)
            break 1;
          foreach ($orderModel as $order) {   
            $grandTotal = $order->getTotalAmt();
            $paidByGiftCard = $order->getGiftTotalAmt();
            $giftAmount = $this->_getGiftCertificateAmount( $order->getCustomerId(),$tier);
            if($giftAmount > 0)
              $grandTotal = $grandTotal - $giftAmount;

            $lifeSpentAmt =  $grandTotal+$paidByGiftCard; 
            if( $lifeSpentAmt  >= $tier->getTierMinRange() &&  $lifeSpentAmt <= $tier->getTierMaxRange()) { 
              $this->_log(++$i .') Found Tier: Customer Id =>'.$order->getCustomerId().'  Tier =>'. $tier->getTierName() .'  Final Amount =>'.$lifeSpentAmt.'(Breakdown :: grandTotal=>'.$order->getTotalAmt() .' giftAmount=>'.$giftAmount.'  paidByGiftCard=>'.$paidByGiftCard.'),  Range =>'. $tier->getTierMinRange().' to '. $tier->getTierMaxRange() ); 
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId()); 
                $customer->setCustomerTier($tier->getId());
                try {
                    $customer->save(); 
                } catch (Exception $ex) {
                   $this->_log('Update tier Error:'.$ex->getMessage());
                   Mage::throwException($ex->getMessage());
                }
             }  
          } 
          $this->_log('Loop  Memory Usage=>'.$this->_memoryUsage());
          $orderModel->clear();
          $this->_log('Loop Clear Memory Usage=>'.$this->_memoryUsage());
        }  
         
      } 
       
  } 
    
  private function _getTierLevel() { 
      $collection = Mage::getModel('customertier/customertier')->getCollection()
                  ->addFieldToFilter('status',1);
                  /*->setPageSize(10) // number of items to be displayed
                    ->setCurPage(1);// change the number */ 
      foreach ($collection as $value) {
        $value->afterLoad(); 
        $this->tierData[] = $value;
      }
  }

    private function _memoryUsage() { 
        $mem_usage = memory_get_usage(false); 
        
        if ($mem_usage < 1024) 
            return $mem_usage." bytes"; 
        elseif ($mem_usage < 1048576) 
            return round($mem_usage/1024,2)." kilobytes"; 
        else 
            return round($mem_usage/1048576,2)." megabytes";  
        
    } 



    /**
     * Gets the gift certificate amount.
     *
     * @param      <int>  $customerId  The customer identifier
     * @param      <object>  $tier      The tier data object
     *
     * @return     <int>  Returns the gift certificate amount.
     */
    private function _getGiftCertificateAmount($customerId,$tier){
 
      $orderModel = Mage::getModel('sales/order')->getCollection()
                              ->addAttributeToSelect('customer_id')  
                                ->join(array('item' => 'sales/order_item'), 'main_table.entity_id = item.order_id AND item.product_type  IN("giftcard")', array('order_id' => 'order_id'))   
                              ->addAttributeToFilter('main_table.status', array('in' => array($tier->getData('order_status'))))  
                              ->addAttributeToFilter('main_table.customer_id',  array('eq' =>  $customerId));  
     if(in_array(0, $tier->getStoreId()))
        $orderModel->addAttributeToFilter('main_table.store_id', array('nin' => $tier->getStoreId()));
      else
        $orderModel->addAttributeToFilter('main_table.store_id', array('in' => $tier->getStoreId()));   
      $orderModel->addExpressionFieldToSelect("item_total", "sum({{base_row_total_incl_tax}})", "base_row_total_incl_tax"); 

      $orderData = $orderModel->getSelect()->group(array('main_table.customer_id'));

      foreach ($orderModel as $order) {   
         return  $order->getItemTotal();
      } 
  
    }

  /**
  * _log Creates the log for this observer.
  *
  * @param      String  $msg   The message that need store in log file
  * 
  */
  private function _log($msg){
    Mage::log($msg, null, "tierCronJob.log");
  } 
 

}