<?php
require_once 'shell/abstract.php';
class Mage_Shell_ReviewUpdate extends Mage_Shell_Abstract
{
    private $ratingCode ='';
    private $batch =0;
    private $ratingIds ='';
    public function run() {
        if ($this->getArg('review_id') ) {
            $reviewId = $this->getArg('review_id');  
        }
        if ($this->getArg('limit') ) {
            $limit = $this->getArg('limit');  
        }
        if (isset($limit) && isset($reviewId)){ 
            $this->ratingCode = $this->getRatingId('rating_code like "%Rating%"');  
            $this->ratingIds =  $this->getRatingId();  
            $this->getReviewCollection($reviewId, $limit);
        }
        else {
            echo $this->usageHelp();
        }
    }

    private function getReviewCollection($reviewId, $limit)
    { 
         $this->batch = ++$this->batch;
         $this->shellMsg('##################BATCH - '.$this->batch  .'###############'); 
         $lastReview_id =0;
         if($reviewId && $limit){
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $query = 'SELECT review_id FROM review WHERE status_id  =  1 AND review_id > '.$reviewId.' LIMIT '. $limit; 
            $reviewResults = $readConnection->fetchAll($query); 
            $count = count($reviewResults);
            $this->shellMsg('Total record: '.$count.' for this batch#'.$this->batch);
            $count_i =0;
            if($count > 0){ 
               
                foreach ($reviewResults as $key => $value) {
                     $count_i++;
                    if($value["review_id"]){
                        $reviewId = $value["review_id"];
                        $votes = $this->getRatingValue($reviewId);
                        $defaulVotes =array();;

                        foreach ($votes as $vkey => $vValue) {
                            if($this->ratingCode[0] == $vValue['rating_id']);
                                $defaulVotes = $vValue;
                            
                        } 
                   
                        if(count($votes)  < count($this->ratingIds)  &&  $reviewId && count($defaulVotes) > 0){ 
                            $review = Mage::getModel('review/review')->load($reviewId);   
                            if ( $review->getId()  ) { 
                                try {   
                                    $arrRatingId = $this->getOptionId($defaulVotes['value'] , $this->ratingIds);  
                                    $data = array(); 
                                    $data['ratings'] = $arrRatingId;
                                    $review->addData($data)->save(); 
                                    $i = 0;
                                  
                                    foreach ($arrRatingId as $ratingId => $optionId) {  
                                    
                                            if($defaulVotes['option_id'] ==  $optionId) {
                                                 #updating existing  rating.
                                             // Mage::getModel('rating/rating')
                                             //    ->setVoteId($vote)
                                             //    ->setReviewId($review->getId())
                                             //    ->updateOptionVote($optionId); 
                                        } else {
 
                                            $i++;
                                              #Add new rating.
                                             Mage::getModel('rating/rating')
                                                ->setRatingId($ratingId)
                                                ->setReviewId($review->getId()) 
                                                ->addOptionVote($optionId, $review->getEntityPkValue());

                                        }
                                    }
                                    $review->aggregate();  
                                    if($i > 0){ 
                                        $this->shellMsg('# '.$count_i.'th The review # '.$reviewId.' has been saved  for this batch#'.$this->batch); 
                                    } 
                                }
                                catch (Mage_Core_Exception $e) {
                                    $this->shellMsg($e->getMessage().' batch#'.$this->batch);  
                                } catch (Exception $e){
                                    $this->shellMsg('<font color="red">An error occurred while saving this review # '.$reviewId.'. '.$e->getMessage().'</font> batch#'.$this->batch); 
                                }
                        }
                    } 

                    if($count_i == $count){  
                      $lastReview_id = $reviewId ;

                    }
                }
             } 
             if($count_i == $count && $lastReview_id <> 0 ){
                $this->getReviewCollection($lastReview_id, $limit);
             }
           }
        }
    }
    private function getRatingId($whereCondition=null){
        $resource = Mage::getSingleton('core/resource');    
        $readConnection = $resource->getConnection('core_read'); 
        $query = 'SELECT * FROM rating '.(($whereCondition) ?  ' WHERE  '.$whereCondition: '');
        $rating = $readConnection->fetchCol($query); 
        return  $rating;
    }

    private function getRatingValue($review_id, $rating_id=''){
        $resource = Mage::getSingleton('core/resource');    
        $readConnection = $resource->getConnection('core_read'); 
        $query = 'SELECT value, option_id, rating_id FROM  rating_option_vote where review_id ='.$review_id;
        if($rating_id)
             $query .= ' and rating_id='.$rating_id;
        $ratingValue = $readConnection->fetchAll($query); 

        return  $ratingValue;
    } 

    private function getOptionId($value,$ratingIds){
        $resource = Mage::getSingleton('core/resource');    
        $readConnection = $resource->getConnection('core_read'); 
        $query = 'SELECT 
                    RO.option_id,
                    R.rating_id
                FROM
                    rating AS R
                        INNER JOIN
                    rating_option AS RO ON RO.rating_id = R.rating_id
                        INNER JOIN
                    rating_store AS RS ON RO.rating_id = RS.rating_id
                WHERE
                    R.entity_id = 1 AND RS.store_id IN (0)
                        AND RO.value = '.$value.'
                        AND R.rating_id IN('.implode(',',$ratingIds).')
                ';
              
        $options = $readConnection->fetchAll($query);
        $optionArray=array();
        foreach ($options as $key => $value) { 
            $optionArray =   array($value['rating_id']=>$value['option_id']) + $optionArray; 
        }
        
        return   $optionArray;
    }
    private function shellMsg($msg){
        Mage::log($msg, null, 'review_update.log');
        echo $msg;
        echo $e . "\n";
    }

    /**
    * Retrieve Usage Help Message
    *
    */
    public function usageHelp()
    {
        return <<<USAGE
        Usage:  php review_update.php -- [review_id] -- [limit]
        review_id   Specified review id to start from that onwards.
        limit   Specified limit to start from that onwards.
USAGE;
    }
    
}
$shell = new Mage_Shell_ReviewUpdate();
$shell->run();