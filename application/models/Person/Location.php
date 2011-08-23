<?php
/**
 * Stores data pertaining to a person's location. Note that this differs from
 * {@link Application_Model_Person_Address}.
 *
 * You will almost never have to use the setter methods of this class directly,
 * since this is done by {@link Application_Model_Person::setLocation()}.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Location extends Application_Model_Abstract {
    /**
     * The location's label.
     * 
     * @var string
     */
    protected $label     = '';

    /**
     * The last time the location was updated.
     *
     * @var string
     */
    protected $updatedAt = '';

    /**
     * The location's latitude.
     * 
     * @var float
     */
    protected $latitude  = 0.0;

    /**
     * The location's longitude.
     *
     * @var float
     */
    protected $longitude = 0.0;

    /**
     * The accuracy of the latitude and longitude.
     *
     * @var float
     */
    protected $accuracy  = 0.0;

    /**
     * Set a location's label.
     *
     * @param string $label
     *
     * @return Application_Model_Person_Location
     */
    public function setLabel($label) {
        $this->label = (string) $label;
        return $this;
    }

    /**
     * Get a location's label.
     *
     * @see Application_Model_Person_Location::setLabel()
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Set the last time a person's location was updated.
     *
     * @param string $updatedAt Time in UTC format.
     *
     * @return Application_Model_Person_Location
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = (string) $updatedAt;
        return $this;
    }

    /**
     * Get the last time a person's location was updated.
     *
     * @see Application_Model_Person_Location::setUpdatedAt()
     * 
     * @return string Time in UTC format.
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set a location's latitude.
     *
     * @param float $latitude
     *
     * @return Application_Model_Person_Location
     */
    public function setLatitude($latitude) {
        $this->latitude = (float) $latitude;
        return $this;
    }

    /**
     * Get a location's latitude.
     *
     * @see Application_Model_Person_Location::setLatitude()
     * 
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Set a location's longitude.
     * 
     * @param string $longitude
     *
     * @return Application_Model_Person_Location
     */
    public function setLongitude($longitude) {
        $this->longitude = (float) $longitude;
        return $this;
    }

    /**
     * Get a location's longitude.
     *
     * @see Application_Model_Person_Location::setLongitude()
     * 
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * Set a location's accuracy.
     *
     * @param flaot $accuracy
     *
     * @return Application_Model_Person_Location
     */
    public function setAccuracy($accuracy) {
        $this->accuracy = (float) $accuracy;
        return $this;
    }

    /**
     * Get a location's accuracy.
     *
     * @see Application_Model_Person_Location::setAccuracy()
     * 
     * @return float
     */
    public function getAccuracy() {
        return $this->accuracy;
    }
}