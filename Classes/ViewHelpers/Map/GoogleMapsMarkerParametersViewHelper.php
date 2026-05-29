<?php
declare(strict_types=1);

namespace WapplerSystems\Address\ViewHelpers\Map;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WapplerSystems\Address\Domain\Model\Address;


class GoogleMapsMarkerParametersViewHelper extends AbstractViewHelper
{


    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('address', Address::class, 'The address', true);
    }

    public function render(): string
    {
        if (!$this->arguments['address'] || !$this->arguments['address']->getLatitude() || !$this->arguments['address']->getLongitude()) {
            return '{}';
        }

        $a = [
            'position' => ['lat' => $this->arguments['address']->getLatitude(), 'lng' => $this->arguments['address']->getLongitude()],
            'title' => $this->arguments['address']->getName()
        ];

        return json_encode($a);
    }

}
