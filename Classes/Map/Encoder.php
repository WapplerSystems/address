<?php
namespace WapplerSystems\Address\Map;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Encoder {
	
	
	private $settings = array();

	public function __construct($settings) {
		$this->settings = $settings;
		
	}


    /**
     * @param $addressQuery
     * @return null|string
     */
    public function getCoordinatesByAddress($addressQuery) {
		$coordinate = null;
		$geocodeUrl = $this->settings['googlemaps']['geocode']['apiUrl'];
		$geocodeUrl .= '?sensor=false&address=' . urlencode(str_replace(LF, ', ', $addressQuery));
		$geocodeResult = GeneralUtility::getURL($geocodeUrl);
		$geocodeResult = json_decode($geocodeResult);
		if ($geocodeResult !== null && strtolower($geocodeResult->status) === 'ok') {
			$coordinate = $geocodeResult->results[0]->geometry->location->lat . ',' . $geocodeResult->results[0]->geometry->location->lng;
		}
		return $coordinate;
	}


    /**
     * @param $address
     * @param $countryCode
     * @return null|LatLng
     */
    public function getLatLngByAddress($address,$countryCode) {
		$latLng = null;
		$geocodeUrl = $this->settings['googlemaps']['geocode']['apiUrl'];
		$geocodeUrl .= '?sensor=false&address=' . urlencode(str_replace(LF, ', ', $address));
        if ($countryCode) {
            $geocodeUrl .= '&components=country:' .$countryCode;
        }
		$geocodeResult = GeneralUtility::getURL($geocodeUrl);
		$geocodeResult = json_decode($geocodeResult);
		
		if ($geocodeResult !== null && strtolower($geocodeResult->status) === 'ok') {
			
			$latLng = new LatLng($geocodeResult->results[0]->geometry->location->lat, $geocodeResult->results[0]->geometry->location->lng);
		} else {
			throw new \RuntimeException('no result');
		}
		return $latLng;
	}
	

	
}