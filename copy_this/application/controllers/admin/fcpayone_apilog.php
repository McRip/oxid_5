<?php

/**
 * This admin view displays the request and the response for a selected API transaction
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class fcpayone_apilog extends oxAdminDetails {

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_apilog.tpl';

    /**
     * Array with existing status of order
     *
     * @var array
     */
    protected $_aStatus = null;

    /**
     * Loads transaction log entry with given oxid, passes
     * it's data to Smarty engine and returns name of template file
     * "fcpayone_apilog.tpl".
     *
     * @return string
     */
    public function render() {
        parent::render();

        $oLogEntry = oxNew( "fcporequestlog" );

        $sOxid = oxConfig::getParameter( "oxid");
        if ( $sOxid != "-1" && isset( $sOxid)) {
            // load object
            $oLogEntry->load( $sOxid);
            $this->_aViewData["edit"] = $oLogEntry;
        }

        $this->_aViewData['sHelpURL'] = 'http://www.payone.de';

        return $this->_sThisTemplate;
    }

}