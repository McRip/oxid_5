<?php

/**
 * Module for core class oxuser
 * Handles the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend oxuser
 */
class fcPayOneUser extends fcPayOneUser_parent {

    /**
     * Sets the credit-worthiness of the user
     *
     * @param array $aResponse response of a API request
     *
     * @return null
     */
    protected function fcpoSetBoni($aResponse) {
        $boni = 100;
        if($aResponse['scorevalue']) {
            $boni = $aResponse['scorevalue'];
        } else {
            if($aResponse['score']) {
                if($aResponse['score'] == 'G') {
                    $boni = 500;
                } elseif($aResponse['score'] == 'Y') {
                    $boni = 300;
                } elseif($aResponse['score'] == 'R') {
                    $boni = 100;
                }
            }
        }
        $this->oxuser__oxboni->value = $boni;
        if($aResponse && is_array($aResponse) && array_key_exists('fcWrongCountry', $aResponse) === false ) {
			if ( !isset( $this->oxuser__fcpobonicheckdate->value ) ) {
				$this->oxuser__fcpobonicheckdate = new oxField( date('Y-m-d H:i:s') );
			}
			else {
				$this->oxuser__fcpobonicheckdate->value = date('Y-m-d H:i:s');
			}
        }
        $this->save();
    }

    /**
     * Check if the credit-worthiness of the user has to be checked again
     *
     * @return bool
     */
    protected function isNewBonicheckNeeded() {
        $sTimeLastCheck = strtotime($this->oxuser__fcpobonicheckdate->value);
        $sTimeout = (time()-(60*60*24*$this->getConfig()->getConfigParam('sFCPODurabilityBonicheck')));
        if($sTimeout > $sTimeLastCheck) {
            return true;
        }
        return false;
    }

    /**
     * Check if the current basket sum exceeds the minimum sum for the credit-worthiness check
     *
     * @return bool
     */
    protected function isBonicheckNeededForBasket() {
        $iStartlimitBonicheck = $this->getConfig()->getConfigParam('sFCPOStartlimitBonicheck');
        if($iStartlimitBonicheck && is_numeric($iStartlimitBonicheck)) {
            $oBasket = $this->getSession()->getBasket();
            if($oBasket->getPrice()->getBruttoPrice() < $iStartlimitBonicheck) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the credit-worthiness has to be checked
     *
     * @return bool
     */
    protected function isBonicheckNeeded() {
        if(($this->oxuser__oxboni->value == $this->getBoni() || $this->isNewBonicheckNeeded()) && $this->isBonicheckNeededForBasket()) {
            return true;
        }
        return false;
    }

    /**
     * Check the credit-worthiness of the user with the consumerscore or addresscheck request to the PAYONE API
     *
     * @return bool
     */
    public function checkAddressAndScore($blCheckAddress = true, $blCheckBoni = true) {
        $oConfig = $this->getConfig();
        $aResponse = array();
        $blCheckedBoni = false;
        if($blCheckBoni === true && $oConfig->getConfigParam('sFCPOBonicheck') != '-1') {
            //Consumerscore
            if($this->isBonicheckNeeded()) {
                $oPORequest = oxNew('fcporequest');
                $aResponse = $oPORequest->sendRequestConsumerscore($this);
                $this->fcpoSetBoni($aResponse);
                $blCheckedBoni = true;
            }
        }
        
        if($blCheckAddress === true && $oConfig->getConfigParam('sFCPOAddresscheck') != 'NO') {
            //Addresscheck
            if($oConfig->getConfigParam('sFCPOBonicheck') == '-1' || $blCheckedBoni === false) {
                //Check Rechnungsadresse
                $oPORequest = oxNew('fcporequest');
                $aResponse = $oPORequest->sendRequestAddresscheck($this);
            }
            if($aResponse === true) {
                $blIsValidAddress = true;
            } else {
                $blIsValidAddress = $this->fcpoIsValidAddress($aResponse, $oConfig->getConfigParam('blFCPOCorrectAddress'));
            }
            if($blIsValidAddress && $oConfig->getConfigParam('blFCPOCheckDelAddress') === true) {
                //Check Lieferadresse
                $oPORequest = oxNew('fcporequest');
                $aResponse = $oPORequest->sendRequestAddresscheck($this, true);
                if($aResponse === false || $aResponse === true) {
                    // false = No deliveryaddress given
                    // true = Address-check has been skipped because the address has been checked before
                    return true;
                }
                $blIsValidAddress = $this->fcpoIsValidAddress($aResponse, false);
            }
            return $blIsValidAddress;
        }
        return true;
    }

    /**
     * Overrides oxid standard method getBoni()
     * Sets it to value defined in the admin area of PAYONE if it was configured
     *
     * @return int
	 * @extend getBoni()
     */
    public function getBoni() {
        $iDefaultBoni = $this->getConfig()->getConfigParam('sFCPODefaultBoni');
        if($iDefaultBoni !== null && is_numeric($iDefaultBoni) === true) {
            return $iDefaultBoni;
        }
        return parent::getBoni();
    }

    /**
     * Checks if the address given by the user matches the address returned by the PAYONE addresscheck API request
     *
     * @return bool
     */
    protected function fcpoIsValidAddress($aResponse, $blCorrectUserAddress) {
        if($aResponse && is_array($aResponse) && array_key_exists('fcWrongCountry', $aResponse) && $aResponse['fcWrongCountry'] === true) {
            return true;
        }
        $oLang = oxLang::getInstance();
        if($aResponse['status'] == 'VALID') {
            if($this->getConfig()->getConfigParam('blFCPOAddCheck'.$aResponse['personstatus'])) {
                $sErrorMsg = $oLang->translateString('FCPO_ADDRESSCHECK_FAILED1').$oLang->translateString('FCPO_ADDRESSCHECK_'.$aResponse['personstatus']).$oLang->translateString('FCPO_ADDRESSCHECK_FAILED2');
                oxUtilsView::getInstance()->addErrorToDisplay($sErrorMsg, false, true);
                return false;
            } else {
                if($blCorrectUserAddress) {
                    if($aResponse['firstname']) {
                        $this->oxuser__oxfname->value = $aResponse['firstname'];
                    }
                    if($aResponse['lastname']) {
                        $this->oxuser__oxlname->value = $aResponse['lastname'];
                    }
                    if($aResponse['streetname']) {
                        $this->oxuser__oxstreet->value = $aResponse['streetname'];
                    }
                    if($aResponse['streetnumber']) {
                        $this->oxuser__oxstreetnr->value = $aResponse['streetnumber'];
                    }
                    if($aResponse['zip']) {
                        $this->oxuser__oxzip->value = $aResponse['zip'];
                    }
                    if($aResponse['city']) {
                        $this->oxuser__oxcity->value = $aResponse['city'];
                    }
                    $this->save();
                }
                #Country auch noch ?!? ( umwandlung iso nach id )
                #$this->oxuser__oxfname->value = $aResponse['country'];
                return true;
            }
        } elseif($aResponse['status'] == 'INVALID') {
            $sErrorMsg = $oLang->translateString('FCPO_ADDRESSCHECK_FAILED1').$aResponse['customermessage'].$oLang->translateString('FCPO_ADDRESSCHECK_FAILED2');
            oxUtilsView::getInstance()->addErrorToDisplay($sErrorMsg, false, true);
            return false;
        } elseif($aResponse['status'] == 'ERROR') {
            $sErrorMsg = $oLang->translateString('FCPO_ADDRESSCHECK_FAILED1').$aResponse['customermessage'].$oLang->translateString('FCPO_ADDRESSCHECK_FAILED2');
            oxUtilsView::getInstance()->addErrorToDisplay($sErrorMsg, false, true);
            return false;
        }
        return true;
    }

}