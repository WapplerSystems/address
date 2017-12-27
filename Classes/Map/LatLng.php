<?php
namespace WapplerSystems\Address\Map;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class LatLng {

	/**
	 * Coordinates preg pattern.
	 */
	const COORDINATES_POINT_PATTERN = '/^-?\d+\.?\d*$/';
	const COORDINATES_SINGLE_PATTERN = '/^-?\d+\.?\d*\s*,\s*-?\d+\.?\d*$/';
	const COORDINATES_ARRAY_PATTERN = '/^(-?\d+\.?\d*\s*,\s*-?\d+\.?\d*\n?)*/';

	/**
	 * @var float
	 */
	protected $latitude;

	/**
	 * @var float
	 */
	protected $longitude;

	/**
	 * Returns TRUE if given coordinate string is valid.
	 *
	 * @param string $coordinate
	 * @return boolean
	 */
	public static function isValidPoint($coordinate) {
		return (preg_match(self::COORDINATES_POINT_PATTERN, $coordinate) !== 0);
	}

	/**
	 * Returns TRUE if given coordinate string is valid.
	 *
	 * @param string $coordinate
	 * @return boolean
	 */
	public static function isValidCoordinate($coordinate) {
		return (preg_match(self::COORDINATES_SINGLE_PATTERN, $coordinate) !== 0);
	}

	/**
	 * Returns TRUE if given coordinates array string is valid.
	 *
	 * @param string $coordinates
	 * @return boolean
	 */
	public static function isValidCoordinatesArray($coordinates) {
		return (preg_match(self::COORDINATES_ARRAY_PATTERN, $coordinates) !== 0);
	}

	/*
	 * Constructor.
	 * Use first parameter as a comma seperated location like "('48.209206,16.372778')", 
	 * or use both parameters like "(48.209206, 16.372778)". 
	 * 
	 * @param mixed $latitude
	 * @param float $longitude
	 */
	public function __construct($latitude, $longitude = NULL) {
		$this->setLatLng($latitude, $longitude);
	}

	/**
	 * Sets this latitude.
	 *
	 * @param float $latitude
	 * @return void
	 */
	public function setLatitude($latitude) {
		$this->latitude = (float) $latitude;
	}

	/**
	 * Returns this latitude.
	 *
	 * @return float
	 */
	public function getLatitude() {
		return (float) $this->latitude;
	}

	/**
	 * Sets this longitude.
	 *
	 * @param float $longitude
	 * @return void
	 */
	public function setLongitude($longitude) {
		$this->longitude = (float) $longitude;
	}

	/**
	 * Returns this longitude.
	 *
	 * @return float
	 */
	public function getLongitude() {
		return (float) $this->longitude;
	}

    /**
     * Sets this latitude and longitude.
     *
     * @param mixed $latitude
     * @param float $longitude
     * @return void
     * @throws \Exception
     */
	public function setLatLng($latitude, $longitude = NULL) {
		if ($longitude === NULL) {
			if (self::isValidCoordinate($latitude) === FALSE) {
				throw new \Exception('Invalid property value for LatLng::setLatLng ("' . $latitude . '") given.', 1294069148);
			}
			@list($latitude, $longitude) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $latitude);
		} else if (self::isValidPoint($latitude) === FALSE || self::isValidPoint($longitude) === FALSE) {
			throw new \Exception('Invalid property value for LatLng::setLatLng ("' . $latitude . ', ' . $longitude . '") given.', 1294069149);
		}
		$this->latitude = (float) $latitude;
		$this->longitude = (float) $longitude;
	}

	/**
	 * Returns the LatLng as JavaScript string.
	 *
	 * @return string
	 */
	public function getPrint() {
		return sprintf('new google.maps.LatLng(%f, %f)', $this->latitude, $this->longitude);
	}

	/**
	 * Returns the LatLng as JavaScript string.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->getPrint();
	}

}
