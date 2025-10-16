<?php

namespace WapplerSystems\Address\Backend\Form\Element;


use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use WapplerSystems\Address\Service\TypoScriptService;

class MapElement extends AbstractFormElement
{


    public function __construct(readonly private TypoScriptService $typoScriptService)
    {

    }

    /**
     * Renders the Google map.
     *
     * @return array
     */
    public function render(): array
    {
        $languageService = $this->getLanguageService();

        $typoscript = null;
        if ($this->data['site'] instanceof Site) {
            $typoscript = $this->typoScriptService->getTypoScript($this->data['parentPageRow']['uid'], $this->data['request'], 0, $this->data['rootline'], $this->data['site']);
        }
        $typoscript = $typoscript->toArray();
        $pluginSettings = $typoscript['plugin.']['tx_address.']['settings.'] ?? [];

        $elementId = StringUtility::getUniqueId('formengine-address-map-');

        $table = $this->data['tableName'];
        $fieldName = $this->data['fieldName'];
        $row = $this->data['databaseRow'];
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();

        $itemValue = $parameterArray['itemFormElValue'];
        $config = $parameterArray['fieldConf']['config'];

        $googleMapsLibrary = '';
        if ($pluginSettings['googlemaps.']['javascript.']['apiUrl'] ?? false) {

            $googleMapsLibrary = $pluginSettings['googlemaps.']['javascript.']['apiUrl'] ?
                htmlentities($pluginSettings['googlemaps.']['javascript.']['apiUrl']) :
                '//maps.google.com/maps/api/js?v=weekly';

            if ($pluginSettings['googlemaps.']['javascript.']['apiKey'] ?? false) {
                $googleMapsLibrary .= '&key=' . $pluginSettings['googlemaps.']['javascript.']['apiKey'];
            }
        }
        $googleMapsLibrary .= '&libraries=marker';

        $out = [];
        $address = $row[$config['parameters']['address']];
        $city = $row[$config['parameters']['city']];
        $country = $row[$config['parameters']['country']];
        $zip = $row[$config['parameters']['zip']];

        $address = preg_replace("/[\n\r]/", ' ', $address);

        if ($zip) $address .= ', ' . $zip;
        if ($city) $address .= ', ' . $city;
        if ($country) $address .= ', ' . $country;

        $addressId = $elementId . '_address';
        $geocodeButtonId = $elementId . '_geocode-button';
        $mapId = $elementId . '_map';

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

        if ($googleMapsLibrary !== '') {
            $out[] = '<script type="text/javascript" src="' . $googleMapsLibrary . '"></script>';
        }

        $out[] = '<div id="' . $elementId . '">';
        $out[] = '
            <input id="' . $addressId . '" type="textbox" value="' . $address . '" style="width:300px">
            <input id="'.$geocodeButtonId.'" class="tx_address_geocode-button" type="button" value="' . $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang.xlf:btn.update') . '">
        ';
        $out[] = '<div class="tx_address_map" data-geocode-button="'.$geocodeButtonId.'" data-address-field="'.$addressField.'" data-longitude-field="'.$longitudeField.'" data-latitude-field="'.$latitudeField.'" data-longitude-control-field="'.$longitudeControlField.'" data-latitude-control-field="'.$latitudeControlField.'" id="' . $mapId . '" style="height:400px;margin:10px 0;width:100%"></div>';
        $out[] = '</div>'; // id=$baseElementId

        $resultArray = [];
        $resultArray['html'] = implode('', $out);

        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@wapplersystems/address/form-engine/element/map-element.js');


        return $resultArray;
    }


    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }


    private function getTypoScriptSettings()
    {
        $tsArray = GeneralUtility::makeInstance(ConfigurationManager::class)
            ->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );

        return GeneralUtility::removeDotsFromTS($tsArray['plugin.']['tx_address.']['settings.'] ?? []);
    }

}
