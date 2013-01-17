<?php

/**
 * Handles configuration for payment protection mechanisms
 * 
 * @author FATCHIP GmbH | Robert Müller
 */
class fcpayone_boni_main extends oxAdminDetails {

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_boni_main.tpl';

    
    /**
     * Loads payment protection configuration and passes them to Smarty engine, returns
     * name of template file "fcpayone_boni_main.tpl".
     *
     * @return string
     */
    public function render() {
        $sReturn = parent::render();

        $oConfig  = $this->getConfig();
        $sOxid = $oConfig->getShopId();

        $aConfBools = array();
        $aConfStrs = array();

        $oDb = oxDb::getDb();
        $sQuery = "select oxvarname, oxvartype, DECODE( oxvarvalue, ".$oDb->quote( $oConfig->getConfigParam( 'sConfigKey' ) ).") as oxvarvalue from oxconfig where oxshopid = '$sOxid' AND (oxvartype = 'str' OR oxvartype = 'bool' )";
        $oResult = $oDb->Execute($sQuery);
        if ($oResult != false && $oResult->recordCount() > 0) {
            $oStr = getStr();
            while (!$oResult->EOF) {
                $sVarName = $oResult->fields[0];
                $sVarType = $oResult->fields[1];
                $sVarVal  = $oResult->fields[2];

                if ($sVarType == "bool")
                    $aConfBools[$sVarName] = ($sVarVal == "true" || $sVarVal == "1");
                if ($sVarType == "str") {
                    $aConfStrs[$sVarName] = $sVarVal;
                    if ( $aConfStrs[$sVarName] ) {
                        $aConfStrs[$sVarName] = $oStr->htmlentities( $aConfStrs[$sVarName] );
                    }
                }

                $oResult->moveNext();
            }
        }

        $this->_aViewData["confbools"] = $aConfBools;
        $this->_aViewData["confstrs"] = $aConfStrs;

        $this->_aViewData['sHelpURL'] = 'http://www.payone.de';

        return $sReturn;
    }

    /**
     * Saves changed configuration parameters.
     *
     * @return mixed
     */
    public function save() {
        $oConfig = $this->getConfig();

        $aConfBools = oxConfig::getParameter( "confbools" );
        $aConfStrs  = oxConfig::getParameter( "confstrs" );

        if ( is_array( $aConfBools ) ) {
            foreach ( $aConfBools as $sVarName => $sVarVal ) {
                $oConfig->saveShopConfVar( "bool", $sVarName, $sVarVal, $sOxId );
            }
        }

        if ( is_array( $aConfStrs ) ) {
            foreach ( $aConfStrs as $sVarName => $sVarVal ) {
                $oConfig->saveShopConfVar( "str", $sVarName, $sVarVal );
            }
        }
    }

}