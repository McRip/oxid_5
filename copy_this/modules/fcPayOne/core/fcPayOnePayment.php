<?php

/**
 * Module for core class oxpayment
 * Handles the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend oxpayment
 */
class fcPayOnePayment extends fcPayOnePayment_parent {

    /**
     * Determines the operation mode ( live or test ) used in this order based on the payment (sub) method
     *
     * @param string $sType payment subtype ( Visa, MC, etc.). Default is ''
     * 
     * @return bool
     */
    public function fcpoGetOperationMode($sType = '') {
        $blLivemode = $this->oxpayments__fcpolivemode->value;
        if($this->getId() == 'fcpocreditcard' && $sType != '') {
            $blLivemode = $this->getConfig()->getConfigParam('blFCPOCC'.$sType.'Live');
        } elseif($this->getId() == 'fcpoonlineueberweisung' && $sType != '') {
            $blLivemode = $this->getConfig()->getConfigParam('blFCPOSB'.$sType.'Live');
        }
        if($blLivemode == true) {
            return 'live';
        } else {
            return 'test';
        }
    }
    
    /**
     * Adds dynvalues to the payone payment type
     * 
     * @extend getDynValues
     * 
     * @return array dyn values
     */
    public function getDynValues() {
        $aDynValues = parent::getDynValues();        
        $aDynValues = $this->_fcGetDynValues($aDynValues);
        
        return $aDynValues;
    }

    /**
     * Adds dynvalues for debitcard payment-method
     * 
     * @param array $aDynValues dynvalues
     * @return array dynvalues (might be modified)
     */
    protected function _fcGetDynValues($aDynValues) {
        if((bool)$this->getConfig()->getConfigParam('sFCPOSaveBankdata') === true) {
            if($this->getId() == 'fcpodebitnote') {
                if ( !is_array( $aDynValues ) ) {
                    $aDynValues = array();
                }
                $oDynValue = new oxStdClass();
                $oDynValue->name = 'fcpo_elv_blz';
                $oDynValue->value = '';
                $aDynValues[] = $oDynValue;
                $oDynValue = new oxStdClass();
                $oDynValue->name = 'fcpo_elv_ktonr';
                $oDynValue->value = '';
                $aDynValues[] = $oDynValue;
                $oDynValue = new oxStdClass();
                $oDynValue->name = 'fcpo_elv_ktoinhaber';
                $oDynValue->value = '';
                $aDynValues[] = $oDynValue;
            }
        }
        return $aDynValues;
    }

}