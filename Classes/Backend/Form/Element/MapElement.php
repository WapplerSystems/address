<?php
namespace WapplerSystems\Address\Backend\Form\Element;


use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class MapElement extends AbstractFormElement
{


    /**
     * Renders the Google map.
     *
     * @return array
     */
    public function render() {
        $languageService = $this->getLanguageService();

        $table = $this->data['tableName'];
        $fieldName = $this->data['fieldName'];
        $row = $this->data['databaseRow'];
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();

        $itemValue = $parameterArray['itemFormElValue'];
        $config = $parameterArray['fieldConf']['config'];

        $version = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
        $pluginSettings = $this->getTypoScriptSettings();


        $googleMapsLibrary = $pluginSettings['googlemaps']['javascript']['apiUrl'] ?
            htmlentities($pluginSettings['googlemaps']['javascript']['apiUrl']) :
            '//maps.google.com/maps/api/js?v=weekly';

        if ($pluginSettings['googlemaps']['javascript']['apiKey']) {
            $googleMapsLibrary .= '&key=' . $pluginSettings['googlemaps']['javascript']['apiKey'];
        }

        $out = [];
        $latitude = (float)$row[$config['parameters']['latitude']];
        $longitude = (float)$row[$config['parameters']['longitude']];
        $address = $row[$config['parameters']['address']];
        $city = $row[$config['parameters']['city']];
        $country = $row[$config['parameters']['country']];
        $zip = $row[$config['parameters']['zip']];

        $address = preg_replace("/[\n\r]/",' ',$address);

        if ($zip) $address .= ', '.$zip;
        if ($city) $address .= ', '.$city;
        if ($country) $address .= ', '.$country;

        $baseElementId = $PA['itemFormElID'] ?? $table . '_map';
        $addressId = $baseElementId . '_address';
        $mapId = $baseElementId . '_map';

        if (!($latitude && $longitude)) {
            $latitude = 0;
            $longitude = 0;
        };
        $dataPrefix = 'data[' . $table . '][' . $row['uid'] . ']';
        $controlPrefix = 'control[active][' . $table . '][' . $row['uid'] . ']';
        $latitudeField = $dataPrefix . '[' . $config['parameters']['latitude'] . ']';
        $latitudeControlField = $controlPrefix . '[' . $config['parameters']['latitude'] . ']';
        $longitudeField = $dataPrefix . '[' . $config['parameters']['longitude'] . ']';
        $longitudeControlField = $controlPrefix . '[' . $config['parameters']['longitude'] . ']';
        $addressField = $dataPrefix . '[' . $config['parameters']['address'] . ']';


        $updateJs = "TBE_EDITOR.fieldChanged('%s','%s','%s','%s');";
        $updateLatitudeJs = sprintf(
            $updateJs,
            $table,
            $row['uid'],
            $config['parameters']['latitude'],
            $latitudeField
        );
        $updateLongitudeJs = sprintf(
            $updateJs,
            $table,
            $row['uid'],
            $config['parameters']['longitude'],
            $longitudeField
        );
        $updateAddressJs = sprintf(
            $updateJs,
            $table,
            $row['uid'],
            $config['parameters']['address'],
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
                arrAddress.forEach(function (address_component) {
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
        document.querySelector('[data-formengine-input-name="' + fieldName + '"]').value = value;
    }
    if (controlFieldName) {
        document.getElementById(controlFieldName).checked = true;
        document.querySelector('[name="' + controlFieldName + '"][type="hidden"]').value = 1;

        document.querySelector('[name="' + controlFieldName + '"]').parentElement.parentElement.parentElement.parentElement.className.replace('disabled','');
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
            <input type="button" value="'.$this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang.xlf:btn.update').'" onclick="TxAddress.codeAddress()">
        ';
        $out[] = '<div id="' . $mapId . '" style="height:400px;margin:10px 0;width:400px"></div>';
        $out[] = '</div>'; // id=$baseElementId

        $resultArray = [];
        $resultArray['html'] = implode('', $out);

        return $resultArray;
    }



    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }


    private function getTypoScriptSettings()
    {
        $tsArray = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(\WapplerSystems\Address\Configuration\ConfigurationManager::class)
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );

        return GeneralUtility::removeDotsFromTS($tsArray['plugin.']['tx_address.']['settings.']);
    }

}
