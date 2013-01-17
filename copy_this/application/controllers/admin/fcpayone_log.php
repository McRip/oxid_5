<?php

/**
 * Displays payment status informations sent by PAYONE
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class fcpayone_log extends oxAdminDetails {

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_log.tpl';

    /**
     * Array with existing status of order
     *
     * @var array
     */
    protected $_aStatus = null;

    /**
     * Loads selected transactions status, passes
     * it's data to Smarty engine and returns name of template file
     * "fcpayone_log.tpl".
     *
     * @return string
     */
    public function render() {
        parent::render();

        $oLogEntry = oxNew( "fcpotransactionstatus" );

        $sOxid = oxConfig::getParameter( "oxid");
        if ( $sOxid != "-1" && isset( $sOxid)) {
            // load object
            $oLogEntry->load( $sOxid);
            $this->_aViewData["edit"] = $oLogEntry;
        }

        $this->_aViewData['sHelpURL'] = 'http://www.payone.de';

        return $this->_sThisTemplate;
    }

    /**
     * Get all transaction status for the given order
     *
     * @param object $oOrder order object
     *
     * @return array
     */
    public function getStatus($oOrder) {
        if(!$this->_aStatus) {
            $oRs = oxDb::getDb()->Execute("SELECT oxid FROM fcpotransactionstatus WHERE fcpo_txid = '{$oOrder->oxorder__fcpotxid->value}' ORDER BY oxid ASC");
            $aStatus = array();
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while (!$oRs->EOF) {
                    $oTransactionStatus = oxNew ('fcpotransactionstatus');
                    $oTransactionStatus->load($oRs->fields[0]);
                    $aStatus[] = $oTransactionStatus;
                    $oRs->moveNext();
                }
            }
            $this->_aStatus = $aStatus;
        }
        return $this->_aStatus;
    }

    /**
     * Triggers capture request to PAYONE API and displays the result
     *
     * @return null
     */
    public function capture() {
        $sOxid = oxConfig::getParameter( "oxid");
        if ( $sOxid != "-1" && isset( $sOxid)) {
            $oOrder = oxNew( "oxorder" );
            $oOrder->load( $sOxid);

            $dAmount = oxConfig::getParameter('capture_amount');
            if($dAmount && $dAmount > 0) {
                $oPORequest = oxNew('fcporequest');
                $oResponse = $oPORequest->sendRequestCapture($oOrder, $dAmount);
                
                $oLang = oxLang::getInstance();
                if($oResponse['status'] == 'APPROVED') {
                    $this->_aViewData["captureMessage"] = '<span style="color: green;">'.$oLang->translateString('FCPO_CAPTURE_APPROVED', null, true).'</span>';
                } elseif($oResponse['status'] == 'ERROR') {
                    $this->_aViewData["captureMessage"] = '<span style="color: red;">'.$oLang->translateString('FCPO_CAPTURE_ERROR', null, true).$oResponse['errormessage'].'</span>';
                }
            }
        }
    }

}