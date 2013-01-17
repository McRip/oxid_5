<?php

/**
 * Core class for API log entries
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class fcporequestlog extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'fcporequestlog';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'fcporequestlog';

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'fcporequestlog' );
    }

    
    /**
     * Get request as array
     * 
     * @return array
     */
    public function getRequestArray() {
        return $this->getArray($this->fcporequestlog__fcpo_request->rawValue);
    }

    /**
     * Get response as array
     * 
     * @return array
     */
    public function getResponseArray() {
        return $this->getArray($this->fcporequestlog__fcpo_response->rawValue);
    }

    /**
     * Get a array from a serialized array or false if not unserializable
     * 
     * @return array
     */
    protected function getArray($sParam) {
        $aArray = unserialize($sParam);
        if(is_array($aArray)) {
            return $aArray;
        }
        return false;
    }

}