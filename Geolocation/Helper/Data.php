<?php
/*
* Monkey Sports Geolocation Init
* Uses MaxMind's GeoIP2 local DB to pull the user's geolocation based off of their IP.
* /-----------------------------------------------------------------------------------/
*
* Gets the users geolocation data and saves it as a magento global Array
* Mage::helper('monkey_geolocation')->monkeyGeolocationInit($_SERVER['REMOTE_ADDR'])
*      access this global: $monkeyGeolocation = Mage::registry('monkey_geolocation_user');
*
* Available functions:
* allen TX IP : 206.193.215.123
* /-------------------------------------/
* Mage::helper('monkey_geolocation')->getUserContinent( $reader->get($ipAddress) );
* Mage::helper('monkey_geolocation')->getUserCountry( $reader->get($ipAddress) );
* Mage::helper('monkey_geolocation')->getUserState( $reader->get($ipAddress) );
* Mage::helper('monkey_geolocation')->getUserPostal( $reader->get($ipAddress) );
* Mage::helper('monkey_geolocation')->getUserCity( $reader->get($ipAddress) );
* Mage::helper('monkey_geolocation')->getUserLocation( $reader->get($ipAddress) );
* Mage::helper('monkey_geolocation')->getUserGeolocation( $reader->get($ipAddress) );
*/

// Inits the MaxMind geolocation loader
require_once Mage::getBaseDir('base').DS.'lib'.DS.'MaxMind'.DS.'GeoIP2'.DS.'vendor'.DS.'autoload.php';
use MaxMind\Db\Reader;

// Standard Magento helper class
class Monkey_Geolocation_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
    * Create global variable from geolocation
    */
    public function monkeyGeolocationInit($ipAddress = '206.193.215.123', $magentoStoresJson){
        // create empty array
        $userGeolocation = array();

        // get geolocation DB
        $databaseFile = Mage::getBaseDir('base').DS.'lib'.DS.'MaxMind'.DS.'GeoIP2'.DS.'vendor'.DS.'geoip2'.DS.'geoip2'.DS.'maxmind-db'.DS.'GeoIP2-City.mmdb';

        // create new reader instance
        $reader = new Reader($databaseFile);

        // set returned geolocation array as variable
        $userGeolocation = array(
            'ipaddress'     => $ipAddress,
            'geolocation'   => $this->getUserGeolocation($reader->get($ipAddress)),
        );

        // close DB session
        $reader->close();

        // returns an array of user distance to each store
        $userDistance = Mage::helper('monkey_geolocation/distance')->monkey_getStoreUserDist($userGeolocation, $magentoStoresJson);

        // return geolocation information
        return $userDistance;
    }

    /**
    * Returns an array of basic user information
    */
    public function monkey_getUserDetails($user) {
        $user = array(
            'country'       => $this->getUserCountry_iso($user['geolocation']),
            'state'         => str_replace(' ', '-', strtolower($this->getUserState($user['geolocation']))),
            'city'          => str_replace(' ', '-', strtolower($this->getUserCity($user['geolocation']))),
            'postal_code'   => $this->getUserPostal($user['geolocation']),
        );

        return $user;
    }

    /**
    * Continent where the user is located on
    */
    public function getUserContinent($array, $lang = 'en') {
        return $array['continent']['names'][$lang];
    }

    /**
    * Country where the user is located on
    */
    public function getUserCountry($array, $lang = 'en') {
        return $array['registered_country']['names'][$lang];
    }

    public function getUserCountry_iso($array) {
        return $array['registered_country']['iso_code'];
    }

    /**
    * State/providence where the user is located on
    */
    public function getUserState($array, $lang = 'en') {
        return $array['subdivisions'][0]['names'][$lang];
    }

    /**
    * Postal code where the user is located on
    */
    public function getUserPostal($array) {
        return $array['postal']['code'];
    }

    /**
    * City the user is located on
    */
    public function getUserCity($array, $lang = 'en') {
        return $array['city']['names'][$lang];
    }

    /**
    * Long/lat for the user
    */
    public function getUserLocation($array) {
        $longlat = array(
            'longitude' => $array['location']['longitude'],
            'latitude' => $array['location']['latitude'],
        );
        return $longlat;
    }

    /**
    * Full user geolocation array
    */
    public function getUserGeolocation($array) {
        return $array;
    }

}
