<?php

/**
 * Module for core class oxpaymentgateway
 * Handles the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend oxpaymentgateway
 */
class fcPayOnePaymentgateway extends fcPayOnePaymentgateway_parent {

    /**
     * Get the next reference number for the upcoming PAYONE transaction
     * 
     * @return int
     */
    protected function getRefNr() {
        $sQuery = "SELECT MAX(fcpo_refnr) FROM fcporefnr";
        $iMaxRefNr = oxDb::getDb()->GetOne($sQuery);
        $iRefNr = (int)$iMaxRefNr + 1;
        $query = "INSERT INTO fcporefnr (fcpo_refnr, fcpo_txid)  VALUES ('{$iRefNr}', '')";
        oxDb::getDb()->Execute($query);

		return $iRefNr;
    }

    /**
     * Determines the operation mode ( live or test ) used for this payment based on payment or form data
     *
     * @param object $oPayment payment object
     * @param string $aDynvalue form data
     * 
     * @return string
     */
    protected function fcpoGetMode($oPayment, $aDynvalue) {
        if($oPayment->getId() == 'fcpocreditcard' || $oPayment->getId() == 'fcpoonlineueberweisung') {
            if($oPayment->getId() == 'fcpocreditcard') {
                return $aDynvalue['fcpo_ccmode'];
            }
            if($oPayment->getId() == 'fcpoonlineueberweisung') {
                $sType = $aDynvalue['fcpo_sotype'];
            }
        }
        return $oPayment->fcpoGetOperationMode($sType);
    }

    /**
     * Overrides standard oxid finalizeOrder method if the used payment method belongs to PAYONE.
     * Return parent's return if payment method is no PAYONE method
     * 
     * Executes payment, returns true on success.
     *
     * @param double $dAmount Goods amount
     * @param object &$oOrder User ordering object
     *
	 * @extend executePayment
     * @return bool
     */
    public function executePayment( $dAmount, &$oOrder ) {
        if($oOrder->isPayOnePaymentType() === false) {
            return parent::executePayment($dAmount, $oOrder);
        }

        $aDynvalue = oxSession::getVar( 'dynvalue' );
        $aDynvalue = $aDynvalue ? $aDynvalue : oxConfig::getParameter( 'dynvalue' );

        $this->_iLastErrorNo = null;
        $this->_sLastError = null;

        $oPORequest = oxNew('fcporequest');
        $oPayment = oxNew('oxpayment');
        $oPayment->load($oOrder->oxorder__oxpaymenttype->value);
        $sAuthorizationType = '';
        
        $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
        if($blPresaveOrder === true) {
            $oOrder->save(false);
        }

        if(!empty($oOrder->oxorder__oxordernr->value)) {
            $iRefNr = $oOrder->oxorder__oxordernr->value;
        } else {
            $iRefNr = $this->getRefNr();
        }
        oxSession::setVar('fcpoRefNr', $iRefNr);
        
        if($oPayment->oxpayments__fcpoauthmode->value == 'authorization') {
            // Authorization
            $response = $oPORequest->sendRequestAuthorization($oOrder, $oOrder->getOrderUser(), $aDynvalue, $iRefNr);
            $sAuthorizationType = 'authorization';
        } else {
            // Preauthorization
            $response = $oPORequest->sendRequestPreauthorization($oOrder, $oOrder->getOrderUser(), $aDynvalue, $iRefNr);
            $sAuthorizationType = 'preauthorization';
        }
        
        $iOrderNotChecked = oxSession::getVar('fcpoordernotchecked');
        if(!$iOrderNotChecked || $iOrderNotChecked != 1) {
            $iOrderNotChecked = 0;
        }
        
        $sMode = $this->fcpoGetMode($oPayment, $aDynvalue);
        if($response['status'] == 'ERROR') {
            $this->_iLastErrorNo = $response['errorcode'];
            $this->_sLastError = $response['customermessage'];
            return false;
        } elseif($response['status'] == 'APPROVED') {
            $oOrder->oxorder__fcpotxid = new oxField($response['txid'], oxField::T_RAW);
            $oOrder->oxorder__fcporefnr = new oxField($iRefNr, oxField::T_RAW);
            $oOrder->oxorder__fcpoauthmode = new oxField($sAuthorizationType, oxField::T_RAW);
            $oOrder->oxorder__fcpomode = new oxField($sMode, oxField::T_RAW);
            $oOrder->oxorder__fcpoordernotchecked->value = new oxField($iOrderNotChecked, oxField::T_RAW);
            oxDb::getDb()->Execute("UPDATE fcporefnr SET fcpo_txid = '{$response['txid']}' WHERE fcpo_refnr = '".$iRefNr."'");
            return true;
        } elseif($response['status'] == 'REDIRECT') {
            oxSession::setVar('fcpoTxid', $response['txid']);
            oxSession::setVar('fcpoAuthMode', $sAuthorizationType);
            oxSession::setVar('fcpoMode', $sMode);
            $oOrder->oxorder__fcpotxid = new oxField($response['txid'], oxField::T_RAW);
            $oOrder->oxorder__fcporefnr = new oxField($iRefNr, oxField::T_RAW);
            $oOrder->oxorder__fcpoauthmode = new oxField($sAuthorizationType, oxField::T_RAW);
            $oOrder->oxorder__fcpomode = new oxField($sMode, oxField::T_RAW);
            $oOrder->oxorder__fcpoordernotchecked->value = new oxField($iOrderNotChecked, oxField::T_RAW);
            if($blPresaveOrder === true) {
                $oOrder->oxorder__oxtransstatus = new oxField('INCOMPLETE');
                $oOrder->oxorder__oxfolder = new oxField('ORDERFOLDER_PROBLEMS');
                $oOrder->save(false);
                oxSession::setVar('fcpoOrderNr', $oOrder->oxorder__oxordernr->value);
            }
            oxUtils::getInstance()->redirect( $response['redirecturl'] );
        }
        return false;
    }

}