<?php

/**
 * Displays all PAYONE payment information for this order
 * Gives the opportunity to trigger the capture of a preauthorized amount of money through the PAYONE API
 * Gives the opportunity to trigger the debit of a given amount of money through the PAYONE API
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class fcpayone_order extends oxAdminDetails {

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_order.tpl';

    /**
     * Array with existing status of order
     *
     * @var array
     */
    protected $_aStatus = null;

    /**
     * Load PAYONE payment information for selected order, passes
     * it's data to Smarty engine and returns name of template file
     * "fcpayone_order.tpl".
     *
     * @return string
     */
    public function render() {
        parent::render();

        $oOrder = oxNew( "oxorder" );

        $sOxid = oxConfig::getParameter( "oxid");
        if ( $sOxid != "-1" && isset( $sOxid)) {
            // load object
            $oOrder->load( $sOxid);
            $this->_aViewData["edit"] = $oOrder;
            $this->_aViewData["status"] = $this->getStatus($oOrder);
        }

        if(oxConfig::getParameter( "status_oxid")) {
            $sStatusOxid = oxConfig::getParameter( "status_oxid");
            $oTransactionStatus = oxNew ('fcpotransactionstatus');
            $oTransactionStatus->load($sStatusOxid);
            if($oOrder->oxorder__fcpotxid->value == $oTransactionStatus->fcpotransactionstatus__fcpo_txid->value) {
                $this->_aViewData["currStatus"] = $oTransactionStatus;
            } else {
                $sStatusOxid = '-1';
            }
        } else {
            $sStatusOxid = '-1';
        }
        $this->_aViewData["status_oxid"] = $sStatusOxid;

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
            
            $blSettleAccount = oxConfig::getParameter("capture_settleaccount");
            if($blSettleAccount === null) {
                $blSettleAccount = true;
            } else {
                $blSettleAccount = (bool)$blSettleAccount;
            }
            
            $oPORequest = oxNew('fcporequest');
            
            $sAmount = oxConfig::getParameter('capture_amount');
            if($sAmount !== null) {
                $dAmount = str_replace(',', '.', $sAmount);
                $oResponse = $oPORequest->sendRequestCapture($oOrder, $dAmount, $blSettleAccount);
            } elseif($aPositions = oxConfig::getParameter('capture_positions')) {
                foreach ($aPositions as $sOrderArtKey => $aOrderArt) {
                    if($aOrderArt['capture'] == '0') {
                        unset($aPositions[$sOrderArtKey]);
                    }
                }
                $oResponse = $oPORequest->sendRequestCapture($oOrder, $dAmount, $blSettleAccount, $aPositions);
            }
            $oLang = oxLang::getInstance();
            if($oResponse && $oResponse['status'] == 'APPROVED') {
                $this->_aViewData["requestMessage"] = '<span style="color: green;">'.$oLang->translateString('FCPO_CAPTURE_APPROVED', null, true).'</span>';
            } elseif($oResponse && $oResponse['status'] == 'ERROR') {
                $this->_aViewData["requestMessage"] = '<span style="color: red;">'.$oLang->translateString('FCPO_CAPTURE_ERROR', null, true).$oResponse['errormessage'].'</span>';
            }
        }
    }

    /**
     * Triggers debit request to PAYONE API and displays the result
     *
     * @return null
     */
    public function debit() {
        $sOxid = oxConfig::getParameter( "oxid");
        if ( $sOxid != "-1" && isset( $sOxid)) {
            $oOrder = oxNew( "oxorder" );
            $oOrder->load( $sOxid);

            $sBankCountry = false;
            if(oxConfig::getParameter('debit_bankcountry')) {
                $sBankCountry = oxConfig::getParameter('debit_bankcountry');
            }
            $sBankAccount = false;
            if(oxConfig::getParameter('debit_bankaccount')) {
                $sBankAccount = oxConfig::getParameter('debit_bankaccount');
            }
            $sBankCode = '';
            if(oxConfig::getParameter('debit_bankcode')) {
                $sBankCode = oxConfig::getParameter('debit_bankcode');
            }
            $sBankaccountholder = '';
            if(oxConfig::getParameter('debit_bankaccountholder')) {
                $sBankaccountholder = oxConfig::getParameter('debit_bankaccountholder');
            }
            
            $sAmount = oxConfig::getParameter('debit_amount');
            
            $oPORequest = oxNew('fcporequest');
            if($sAmount !== null) {
                $dAmount = str_replace(',', '.', $sAmount);
                
                // amount for credit entry has to be negative
                if((double)$dAmount > 0) {
                    $dAmount = (double)$dAmount * -1;
                }

                if($dAmount && $dAmount < 0) {
                    $oResponse = $oPORequest->sendRequestDebit($oOrder, $dAmount, $sBankCountry, $sBankAccount, $sBankCode, $sBankaccountholder);
                }
            } elseif($aPositions = oxConfig::getParameter('capture_positions')) {
                foreach ($aPositions as $sOrderArtKey => $aOrderArt) {
                    if($aOrderArt['debit'] == '0') {
                        unset($aPositions[$sOrderArtKey]);
                    }
                }
                $oResponse = $oPORequest->sendRequestDebit($oOrder, $dAmount, $sBankCountry, $sBankAccount, $sBankCode, $sBankaccountholder, $aPositions);
            }


            $oLang = oxLang::getInstance();
            if($oResponse['status'] == 'APPROVED') {
                $this->_aViewData["requestMessage"] = '<span style="color: green;">'.$oLang->translateString('FCPO_DEBIT_APPROVED', null, true).'</span>';
            } elseif($oResponse['status'] == 'ERROR') {
                $this->_aViewData["requestMessage"] = '<span style="color: red;">'.$oLang->translateString('FCPO_DEBIT_ERROR', null, true).$oResponse['errormessage'].'</span>';
            }
        }
    }

}