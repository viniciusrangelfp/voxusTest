<?php

namespace Voxus\App\controller;

class GeoLocation {
    protected $key = 'AIzaSyCZBJyrkHhbbL78dHX_RXtwolTclkhapKk';

    public $address;
    public function __construct($lat,$long){
        $geolocation = $lat.','.$long;
        $req = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false&key='.$this->key;

        $file_contents = file_get_contents($req);
        $result = json_decode($file_contents);

        if($result->results){
            $geo = $result->results[0];
            $this->address = $geo->formatted_address;
        }
    }
}
