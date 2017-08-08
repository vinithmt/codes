<?php
/*
* Monkey Sports Distance Helper
* Contains functions to calculate distance between stores and user locations
* /-----------------------------------------------------------------------------------/
*
*/

class Monkey_Geolocation_Helper_Distance extends Mage_Core_Helper_Abstract {

    /**
    * Calculates the great-circle distance between two points, with the Vincenty formula.
    * @param float $latitudeFrom Latitude of start point in [deg decimal]
    * @param float $longitudeFrom Longitude of start point in [deg decimal]
    * @param float $latitudeTo Latitude of target point in [deg decimal]
    * @param float $longitudeTo Longitude of target point in [deg decimal]
    * @param float $earthRadius Mean earth radius in [m]
    * @return float Distance between points in [m] (same as earthRadius)
    */
    public function getDistanceDifference($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959) {

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $angle * $earthRadius;
    }

    /**
    * Returns the distance from each store the user is from
    */
    public function monkey_getStoreUserDist($user, $stores) {
        $userLong   = $user['geolocation']['location']['longitude'];
        $userLat    = $user['geolocation']['location']['latitude'];

        $detail = array();
        foreach ($stores as $item) {
            // get store lat/long
            $storeLong   = $item->longtitude;
            $storeLat    = $item->latitude;

            // calculate distance between
            $distance = $this->getDistanceDifference($userLat, $userLong, $storeLat, $storeLong);

            // build store array
            $detail['stores'][$item->name] = array(
                'storename'     => $item->name,
                'address'       => "{$item->address}, {$item->city}, {$item->state}, {$item->country}",
                'distance'      => $distance,
                'latitude'      => $storeLat,
                'longtitude'    => $storeLong,
            );
        }

        $detail['user_details'] = Mage::helper('monkey_geolocation')->monkey_getUserDetails($user);

        return $detail;
    }

    /**
    * Returns the distance from each store the user is from
    */
    public function monkey_selectiveRange($array, $states, $country = 'US', $type) {
        if ( !$array || !$states ) return false;

        // default value
        $allowed = true;

        // test country
        $allowed = ( $array['country'] == $country ? $allowed : false );

        // test states
        foreach ($states as $state) {
            switch($type) {
                case false:
                    $allowed = ( $array['state'] != $state ? $allowed : false );
                    break;
                default :
                    $allowed = ( $array['state'] == $state ? $allowed : false );
                    break;
            };
        }

        // return boolval
        return $allowed;
    }

    /**
    * Cycles through each store and determines if user is within a set range
    */
    public function monkey_closestStoreNiceName($stores, $distance = 100) {
        $inRange = array();

        // create array of stores inside range
        foreach ($stores['stores'] as $key => $item) {
            if ($item['distance'] <= $distance) {
                $inRange[] =  array(
                    'storename' => str_replace(' ', '-', strtolower($item['storename'])),
                    'distance' => $item['distance']
                );
            }
        }

        // if $inRange has more than one result, search through it and find the closest one
        if ( !empty($inRange) && count($inRange) >= 1 ) {
            $minKey = array_search(
                min(array_column($inRange, 'distance')),
                array_column($inRange, 'distance')
            );
        };

        // return nice-name string
        if ( !empty($inRange) )
            return $inRange[$minKey]['storename'];

    }

}
