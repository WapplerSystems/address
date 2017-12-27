<?php

namespace WapplerSystems\Address\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Xavier Perseguers <xavier@causal.ch>, Causal
 *  (c) 2015 Marc Hirdes <hirdes@clicsktorm.de>, clickstorm GmbH
 *  (c) 2017 Sven Wappler <typo3YYYY@wappler.systems>, WapplerSystems
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Google map.
 *
 * @package address
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Core\TypoScript\ExtendedTemplateService;

class LocationUtility {

	/**
	 * Renders the Google map.
	 *
	 * @param array $PA
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $pObj
	 * @return string
	 */
	public function render(array &$PA, $pObj) {
		$version = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
		$settings = $this->loadTS($PA['row']['pid']);
		$pluginSettings = $settings['plugin.']['tx_address.']['settings.'];

		$googleMapsLibrary = $pluginSettings['googlemaps']['javascript']['apiUrl'] ?
			htmlentities($pluginSettings['googlemaps']['javascript']['apiUrl']) :
			'//maps.google.com/maps/api/js?v=3.29';

		if ($pluginSettings['googlemaps']['javascript']['apiKey']) {
			$googleMapsLibrary .= '&key=' . $pluginSettings['googlemaps']['javascript']['apiKey'];
		}

		$out = [];
		$latitude = (float)$PA['row'][$PA['parameters']['latitude']];
		$longitude = (float)$PA['row'][$PA['parameters']['longitude']];
		$address = $PA['row'][$PA['parameters']['address']];
        $city = $PA['row'][$PA['parameters']['city']];
        $country = $PA['row'][$PA['parameters']['country']];
        $zip = $PA['row'][$PA['parameters']['zip']];

        $address = preg_replace("/[\n\r]/",' ',$address);

        if ($zip) $address .= ', '.$zip;
        if ($city) $address .= ', '.$city;
        if ($country) $address .= ', '.$country;

		$baseElementId = $PA['itemFormElID'] ?? $PA['table'] . '_map';
		$addressId = $baseElementId . '_address';
		$mapId = $baseElementId . '_map';

		if (!($latitude && $longitude)) {
			$latitude = 0;
			$longitude = 0;
		};
		$dataPrefix = 'data[' . $PA['table'] . '][' . $PA['row']['uid'] . ']';
        $controlPrefix = 'control[active][' . $PA['table'] . '][' . $PA['row']['uid'] . ']';
		$latitudeField = $dataPrefix . '[' . $PA['parameters']['latitude'] . ']';
        $latitudeControlField = $controlPrefix . '[' . $PA['parameters']['latitude'] . ']';
		$longitudeField = $dataPrefix . '[' . $PA['parameters']['longitude'] . ']';
        $longitudeControlField = $controlPrefix . '[' . $PA['parameters']['longitude'] . ']';
		$addressField = $dataPrefix . '[' . $PA['parameters']['address'] . ']';


		$updateJs = "TBE_EDITOR.fieldChanged('%s','%s','%s','%s');";
		$updateLatitudeJs = sprintf(
			$updateJs,
			$PA['table'],
			$PA['row']['uid'],
			$PA['parameters']['latitude'],
			$latitudeField
		);
		$updateLongitudeJs = sprintf(
			$updateJs,
			$PA['table'],
			$PA['row']['uid'],
			$PA['parameters']['longitude'],
			$longitudeField
		);
		$updateAddressJs = sprintf(
			$updateJs,
			$PA['table'],
			$PA['row']['uid'],
			$PA['parameters']['address'],
			$addressField
		);

		$out[] = '<script type="text/javascript" src="' . $googleMapsLibrary . '"></script>';
		$out[] = '<script type="text/javascript">';
		$out[] = <<<EOT
if (typeof TxAddress == 'undefined') TxAddress = {};

String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ''); } 

TxAddress.init = function() {
	TxAddress.origin = new google.maps.LatLng({$latitude}, {$longitude});
	var myOptions = {
		zoom: 12,
		center: TxAddress.origin,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	TxAddress.map = new google.maps.Map(document.getElementById("{$mapId}"), myOptions);
	TxAddress.marker = new google.maps.Marker({
		map: TxAddress.map,
		position: TxAddress.origin,
		draggable: true
	});
	google.maps.event.addListener(TxAddress.marker, 'dragend', function() {
		var lat = TxAddress.marker.getPosition().lat().toFixed(6);
		var lng = TxAddress.marker.getPosition().lng().toFixed(6);

        // update fields
		TxAddress.updateValue('{$latitudeField}', lat, '{$latitudeControlField}');
        TxAddress.updateValue('{$longitudeField}', lng, '{$longitudeControlField}');

		// Update address
		TxAddress.reverseGeocode(TxAddress.marker.getPosition().lat(), TxAddress.marker.getPosition().lng());
		
		// Update Position
		var position = document.getElementById("{$addressId}");
		position.value = lat + "," + lng;
		
		// Tell TYPO3 that fields were updated
		TxAddress.positionChanged();
	});
	TxAddress.geocoder = new google.maps.Geocoder();

};

TxAddress.refreshMap = function() {
	google.maps.event.trigger(TxAddress.map, 'resize');
	TxAddress.map.setCenter(TxAddress.marker.getPosition());
	// No need to do it again
	Ext.fly(TxAddress.tabPrefix + '-MENU').un('click', TxAddress.refreshMap);
}
/***************************/
TxAddress.codeAddress = function() {
	var address = document.getElementById("{$addressId}").value;
	
	var lat = 0;
	var lng = 0;
	if (address.match(/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/)) {
		// Get Position
		lat = address.substr(0, address.lastIndexOf(',')).trim();
		lng = address.substr(address.lastIndexOf(',')+1).trim();
		position = new google.maps.LatLng(lat, lng);
		
		// Update Map
		TxAddress.map.setCenter(position);
		TxAddress.marker.setPosition(position);
		
		// Update visible fields
		TxAddress.updateValue('{$latitudeField}', lat, '{$latitudeControlField}');
        TxAddress.updateValue('{$longitudeField}', lng, '{$longitudeControlField}');

		// Get Address
		TxAddress.reverseGeocode(lat, lng);
	} else {
		TxAddress.geocoder.geocode({'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				// Get Position
				
				lat = results[0].geometry.location.lat().toFixed(6);
				lng = results[0].geometry.location.lng().toFixed(6);
				
				var arrAddress = results[0].address_components;
                var route='';
                var locality='';
                var country='';
                var postalCode='';
                var streetNumber='';
                
                
                
                // iterate through address_component array
                TYPO3.jQuery.each(arrAddress, function (i, address_component) {
                    if (address_component.types[0] == "route"){
                        route = address_component.long_name;
                    }
                    if (address_component.types[0] == "locality"){
                        locality = address_component.long_name;
                    }
                    if (address_component.types[0] == "country"){ 
                        country = address_component.long_name;
                    }
                    if (address_component.types[0] == "postal_code_prefix"){ 
                        postalCode = address_component.long_name;
                    }
                    if (address_component.types[0] == "street_number"){ 
                        streetNumber = address_component.long_name;
                    }
                });

				formatedAddress = route + ' ' +streetNumber;
				
				// Update Map
				TxAddress.map.setCenter(results[0].geometry.location);
				TxAddress.marker.setPosition(results[0].geometry.location);
				
				// Update fields
                TxAddress.updateValue('{$latitudeField}', lat, '{$latitudeControlField}');
                TxAddress.updateValue('{$longitudeField}', lng, '{$longitudeControlField}');
                TxAddress.updateValue('{$addressField}', formatedAddress);

                TxAddress.positionChanged();
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	}
}

TxAddress.positionChanged = function() {
    {$updateLatitudeJs}
    {$updateLongitudeJs}
    {$updateAddressJs}
    TYPO3.FormEngine.Validation.validate();
}

TxAddress.updateValue = function(fieldName, value, controlFieldName) {
    var version = {$version};
    document[TBE_EDITOR.formname][fieldName].value = value;
    if(version < 7005000) {
        document[TBE_EDITOR.formname][fieldName + '_hr'].value = value;
    } else {
        TYPO3.jQuery('[data-formengine-input-name="' + fieldName + '"]').val(value);
    }
    if (controlFieldName) {
        TYPO3.jQuery('[id="' + controlFieldName + '"]').prop('checked',true);
        TYPO3.jQuery('[name="' + controlFieldName + '"][type="hidden"]').val(1);
        
        TYPO3.jQuery('[name="' + controlFieldName + '"]').first().parent().parent().parent().removeClass('disabled');
    }
}

TxAddress.setMarker = function(lat, lng) {
	var addressInput = document.getElementById("{$addressId}");
	var latlng = new google.maps.LatLng(lat, lng);
	TxAddress.geocoder.geocode({'latLng': latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			TxAddress.map.setCenter(results[0].geometry.location);
			TxAddress.marker.setPosition(results[0].geometry.location);
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}

TxAddress.reverseGeocode = function(latitude, longitude) {
	var latlng = new google.maps.LatLng(latitude, longitude);
	TxAddress.geocoder.geocode({'latLng': latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK && results[1]) {
		    console.debug(results[1]);
		    TxAddress.updateValue('{$addressField}', results[1].formatted_address);
			TxAddress.positionChanged();
		}
	});
}

TxAddress.convertAddress = function(addressOld) {
	addressInput = document.getElementById("{$addressId}");
	
	TxAddress.geocoder.geocode({'address':addressOld}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			TxAddress.map.setCenter(results[0].geometry.location);
			TxAddress.marker.setPosition(results[0].geometry.location);
			var lat = TxAddress.marker.getPosition().lat().toFixed(6);
			var lng = TxAddress.marker.getPosition().lng().toFixed(6);

            TxAddress.updateValue('{$latitudeField}', lat);
            TxAddress.updateValue('{$longitudeField}', lng);

			// Update visible fields
			addressInput.value = addressOld;
			
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}

window.onload = TxAddress.init;
EOT;
		$out[] = '</script>';
		$out[] = '<div id="' . $baseElementId . '">';
		$out[] = '
			<input id="' . $addressId . '" type="textbox" value="' . $address . '" style="width:300px">
			<input type="button" value="Update" onclick="TxAddress.codeAddress()">
		';
		$out[] = '<div id="' . $mapId . '" style="height:400px;margin:10px 0;width:400px"></div>';
		$out[] = '</div>'; // id=$baseElementId

		return implode('', $out);
	}

	protected function loadTS($pageUid) {
		$sysPageObj = GeneralUtility::makeInstance(
            PageRepository::class
		);
		$rootLine = $sysPageObj->getRootLine($pageUid);
		$TSObj = GeneralUtility::makeInstance(
            ExtendedTemplateService::class
		);
		$TSObj->tt_track = 0;
		$TSObj->init();
		$TSObj->runThroughTemplates($rootLine);
		$TSObj->generateConfig();

		return $TSObj->setup;
	}
}