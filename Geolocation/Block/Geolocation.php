<?php
class Monkey_Geolocation_Block_Geolocation extends Mage_Core_Block_Template {

	private  $blockPrefix='';
	private  $selectiveRange ='';
	private  $monkeyGeolocation ='';
	private  $geoBlock ='';

	protected function _construct() {

		// get users IP address
		$ipaddress = $_SERVER['REMOTE_ADDR'];

		#################TESTING##############
		/*
		$input = array(
			'206.193.215.123', // allen
			'129.24.150.63', // new mexico
			'138.128.7.194', // anchorage alaksa
			'98.155.61.204', // hawaii
			'192.206.151.131', // canada,
			'71.127.225.247',//NJ
			'54.37.132.193',//NJ
			'104.144.125.196',//Denver
			'156.98.76.130', // Minnesota
		);
		$rand_keys = array_rand($input, 2);
		$ipaddress	=	$input[$rand_keys[0]];
		*/
	 	#################TESTING##############
		
		if(!Mage::getSingleton('core/session')->getMonkeyGeolocationUser()){
			// Create global variable from geolocation
			// returns array holding user geolocation data in relation to stores
			$userGeolocation = Mage::helper('monkey_geolocation')->monkeyGeolocationInit(
				$ipaddress,
				Mage::helper('monkey_geolocation/stores')->monkey_getListStoreJson()
			);
			Mage::getSingleton('core/session')->setMonkeyGeolocationUser($userGeolocation); // registers global var
		}		
	}

	/**
	* Displays user Geolocation & store information
	* Useage: Mage::getBlockSingleton('geolocation/geolocation')->getGeolocation()
	*/
	public function getGeolocation() {
		$monkeyGeolocation = Mage::getSingleton('core/session')->getMonkeyGeolocationUser();
		$monkeyGeolocation['stores'] = $this->arrayKeyCaseChange($monkeyGeolocation['stores']);
		
		return $monkeyGeolocation;
	}

	/**
	* Displays user Geolocation closeset store
	* Useage: Mage::getBlockSingleton('geolocation/geolocation')->getGeolocationUserStore()
	*/
	public function getGeolocationUserStore() {
		// clear used variables
		$monkeyGeolocation = $monkeyGeolocationStores = $storeNiceName = $userStore = '';
		$userStore = array();
				
		// get geolocation global var
		$monkeyGeolocation = Mage::getSingleton('core/session')->getMonkeyGeolocationUser();
		$monkeyGeolocation['stores'] = $this->arrayKeyCaseChange($monkeyGeolocation['stores']);

		// returns the nicename of the closest store to user if they're in range
		// @param 1 - store list with distance from user IP
		// @param 2 - distance in miles to search for
		$storeNiceName = Mage::helper('monkey_geolocation/distance')->monkey_closestStoreNiceName($monkeyGeolocation, 10000);
		
		// creates new array with store and user information
		$userStore = array(
			'user_store'	=> $monkeyGeolocation['stores'][$storeNiceName],
			'user_details'	=> $monkeyGeolocation['user_details']
		);
		
		return $userStore;
	}

	/**
	* Geolocation slider setup
	*/
	public function getGeolocationBlocks($blockPrefix, $blockType='static') {
		//$this->intConstruct();

		// get geolocation global var
		$this->monkeyGeolocation = Mage::getSingleton('core/session')->getMonkeyGeolocationUser();
		$this->monkeyGeolocation['stores'] = $this->arrayKeyCaseChange($this->monkeyGeolocation['stores']);

		// returns the nicename of the closest store to user if they're in range
		// @param 1 - store list with distance from user IP
		// @param 2 - distance in miles to search for
		$this->geoBlock =  Mage::helper('monkey_geolocation/distance')->monkey_closestStoreNiceName($this->monkeyGeolocation, 100);

		// Filter the results even further if needed
		// This filters results to lower 48 states of the US
		$this->selectiveRange = Mage::helper('monkey_geolocation/distance')->monkey_selectiveRange(
			$this->monkeyGeolocation['user_details'], // array to pull info from
			array('alaska', 'hawaii'),          // states to filter with
			'US',                               // country to filter with
			false                               // state conditonal boolval: true = '==', false = '!='
		);

		// Define class variable
		$this->blockPrefix = $blockPrefix;

		// Define which conditional template to use
		if($blockType == 'aw_islider') {
			// magento AW_iSlider blocks
 			$geoblock_html = $this->getAWSliderBlock();
 		} else {
			// magento static blocks
 			$geoblock_html =  $this->getStaticBlock();
		};

		// return our geolocation block
		return $geoblock_html;
	}

	private function getAWSliderBlock() {

		$geoblock_html = '';

		// if within Lower 48 states and over xxx miles away from Allen or Montreal
		if ( $this->isActiveAWSlider('block_id', $this->blockPrefix.'-lower48') == true && $this->selectiveRange == true && $this->monkeyGeolocation['user_details']['city'] != 'allen' && $this->monkeyGeolocation['user_details']['city'] != 'montreal' ) {
			$geoblock_html = $this->blockPrefix.'-lower48';
		}
		// if geoblock banner is available use it instead of lower48
		if ( $this->geoBlock && $this->isActiveAWSlider('block_id', $this->blockPrefix.'-'.$this->geoBlock) == true ) {
			$geoblock_html = $this->blockPrefix.'-'.$this->geoBlock;
		}

		// get default banner
		if ( !$geoblock_html && $this->isActiveAWSlider('block_id', $this->blockPrefix) == true ) {
			$geoblock_html = $this->blockPrefix;

		}

		return $geoblock_html;
	}

	private function getStaticBlock() {

		$geoblock_html = '';

		// if within Lower 48 states and over xxx miles away from Allen or Montreal
		if ( Mage::getModel('cms/block')->load($this->blockPrefix.'-lower48')->getIsActive() == true && $this->selectiveRange == true && $this->monkeyGeolocation['user_details']['city'] != 'allen' && $this->monkeyGeolocation['user_details']['city'] != 'montreal' ) {
   			$geoblock_html =  Mage::getSingleton('core/layout')->createBlock('cms/block')
									->setBlockId($this->blockPrefix.'-lower48')
									->toHtml();
		}


		// if geoblock banner is available use it instead of lower48
		if ( $this->geoBlock && Mage::getModel('cms/block')->load($this->blockPrefix.'-'.$this->geoBlock)->getIsActive()  == true ) {
				$geoblock_html =   Mage::getSingleton('core/layout')->createBlock('cms/block')
									->setBlockId($this->blockPrefix.'-'.$this->geoBlock)
									->toHtml();
		}

		// get default banner
		if ( !$geoblock_html &&   Mage::getModel('cms/block')->load($this->blockPrefix)->getIsActive() == true ) {
				$geoblock_html =   Mage::getSingleton('core/layout')->createBlock('cms/block')
									->setBlockId($this->blockPrefix)
									->toHtml();
		}

		return $geoblock_html;
	}

	/**
	* Updates array keys to lowercase and convert spaces to dashes	
	*/
	protected function arrayKeyCaseChange($array) {		
		// lowercase and nice name array keys
		$array = array_combine( 
			str_replace(' ', '-', array_keys( array_change_key_case($array, CASE_LOWER)))
			, 
			array_values( $array ) 
		);
		
		return $array;
	}

	/**
	* determines if the slider module ID exist
	*/
	protected function isActiveAWSlider($attribute, $value){
		 $col = Mage::getModel('awislider/slider')->getCollection();
		 $col->addFieldToFilter($attribute, $value);
		 $item = $col->getFirstItem();
		 $id = $item->getData('is_active');

		 if($id == 1){
		     return true;
		 } else {
		     return false;
		 }
 	}

}
