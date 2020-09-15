<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Command;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\Address\Service\GeocodeService;

/**
 * Command for geocoding coordinates
 */
class GeocodeCommand extends Command
{

    /**
     * Defines the allowed options for this command
     *
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Geocode address records')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'Google Maps key for geocoding'
            );
    }

    /**
     * Geocode all records
     *
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getGeocodeService($input->getArgument('key'))->calculateCoordinatesForAllRecordsInTable();
    }

    /**
     * @param string $key Google Maps key
     * @return GeocodeService
     */
    protected function getGeocodeService(string $key)
    {
        return GeneralUtility::makeInstance(GeocodeService::class, $key);
    }
}
