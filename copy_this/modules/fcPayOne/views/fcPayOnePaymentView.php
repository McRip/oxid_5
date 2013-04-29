<?php

/**
 * Module for views class payment
 * Handles the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend payment
 */
class fcPayOnePaymentView extends fcPayOnePaymentView_parent {

    /**
     * bill country id of the user object
     * @var string
     */
    protected $_sUserBillCountryId = null;
    
    /**
     * delivery country id if existant
     * @var string
     */
    protected $_sUserDelCountryId = null;
    
    /**
     * Contains the sub payment methods that are available for the user ( Visa, MC, etc. )
     * @var array
     */
    protected $_aCheckedSubPayments = array();

    /**
     * Extends oxid standard method _filterDynData()
     * Unsets the PAYONE form-data fields containing creditcard data
     * 
     * Due to legal reasons probably you are not allowed to store or even handle credit card data.
     * In this case we just delete and forget all submited credit card data from this point.
     * Override this method if you actually want to process credit card data.
     *
     * Note: You should override this method as setting blStoreCreditCardInfo to true would
     *       force storing CC data on shop side (what most often is illegal).
     *
     * @return null
	 * @extend _filterDynData
     */
    protected function _filterDynData() {
        if($this->_hasFilterDynDataMethod() === true) {
            parent::_filterDynData();
        }

        //in case we actually ARE allowed to store the data
        if (oxConfig::getInstance()->getConfigParam("blStoreCreditCardInfo"))
            //then do nothing
            return;

        $aDynData = $this->getSession()->getVar("dynvalue");

        if ($aDynData) {
            $aDynData["fcpo_kktype"] = null;
            $aDynData["fcpo_kknumber"] = null;
            $aDynData["fcpo_kkname"] = null;
            $aDynData["fcpo_kkmonth"] = null;
            $aDynData["fcpo_kkyear"] = null;
            $aDynData["fcpo_kkpruef"] = null;
            $aDynData["fcpo_kkcsn"] = null;
            $this->getSession()->setVar("dynvalue", $aDynData);
        }


        unset($_REQUEST["dynvalue"]["fcpo_kktype"]);
        unset($_REQUEST["dynvalue"]["fcpo_kknumber"]);
        unset($_REQUEST["dynvalue"]["fcpo_kkname"]);
        unset($_REQUEST["dynvalue"]["fcpo_kkmonth"]);
        unset($_REQUEST["dynvalue"]["fcpo_kkyear"]);
        unset($_REQUEST["dynvalue"]["fcpo_kkpruef"]);
        unset($_REQUEST["dynvalue"]["fcpo_kkcsn"]);

        unset($_POST["dynvalue"]["fcpo_kktype"]);
        unset($_POST["dynvalue"]["fcpo_kknumber"]);
        unset($_POST["dynvalue"]["fcpo_kkname"]);
        unset($_POST["dynvalue"]["fcpo_kkmonth"]);
        unset($_POST["dynvalue"]["fcpo_kkyear"]);
        unset($_POST["dynvalue"]["fcpo_kkpruef"]);
        unset($_POST["dynvalue"]["fcpo_kkcsn"]);

        unset($_GET["dynvalue"]["fcpo_kktype"]);
        unset($_GET["dynvalue"]["fcpo_kknumber"]);
        unset($_GET["dynvalue"]["fcpo_kkname"]);
        unset($_GET["dynvalue"]["fcpo_kkmonth"]);
        unset($_GET["dynvalue"]["fcpo_kkyear"]);
        unset($_GET["dynvalue"]["fcpo_kkpruef"]);
        unset($_GET["dynvalue"]["fcpo_kkcsn"]);
    }
    
    /**
     * Extends oxid standard method init()
     * Executes parent method parent::init().
     *
     * @return null
     */
    public function init() {
        if($this->_hasFilterDynDataMethod() === false) {
            $this->_filterDynData();
        }
        $sOrderId = oxSession::getVar('sess_challenge');
        $sType = oxConfig::getParameter('type');
        $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
        $blReduceStockBefore = !(bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');     
        if($sOrderId && $blPresaveOrder && $blReduceStockBefore && ($sType == 'error' || $sType == 'cancel')) {
            $oOrder = oxNew('oxorder');
            $oOrder->load($sOrderId);
            if($oOrder) {
                $oOrder->cancelOrder();
            }
            unset($oOrder);
        }
        oxSession::deleteVar('sess_challenge');
        return parent::init();
    }
    
    /**
     * Checks whether the oxid version has the _filterDynData method
     * Oxid 4.2 and below dont have the _filterDynData method
     * 
     * @return bool
     */
    protected function _hasFilterDynDataMethod() {
        $oConfig = $this->getConfig();
        $iVersion = (int)str_replace('.', '', $oConfig->getActiveShop()->oxshops__oxversion->value);
        while ($iVersion < 1000) {
            $iVersion = $iVersion*10;
        }
        if($iVersion >= 4300) {
            return true;
        }
        return false;
    }

    /**
     * Gets config parameter
     * 
     * @param string $sParam config parameter name
     * 
     * @return string
     */
    protected function getConfigParam($sParam) {
        $oConfig = $this->getConfig();
        return $oConfig->getConfigParam($sParam);
    }

    /**
     * Get config parameter sFCPOMerchantID
     * 
     * @return string
     */
    public function getMerchantId() {
        return $this->getConfigParam('sFCPOMerchantID');
    }

    /**
     * Get config parameter sFCPOSubAccountID
     * 
     * @return string
     */
    public function getSubAccountId() {
        return $this->getConfigParam('sFCPOSubAccountID');
    }

    /**
     * Get config parameter sFCPOPortalID
     * 
     * @return string
     */
    public function getPortalId() {
        return $this->getConfigParam('sFCPOPortalID');
    }

    /**
     * Get config parameter sFCPOPortalKey
     * 
     * @return string
     */
    public function getPortalKey() {
        return $this->getConfigParam('sFCPOPortalKey');
    }

    /**
     * Get config parameter sFCPOPOSCheck
     * 
     * @return string
     */
    public function getChecktype() {
        return $this->getConfigParam('sFCPOPOSCheck');
    }

    /**
     * Get, and set if needed, the bill country id of the user object
     * 
     * @return string
     */
    protected function getUserBillCountryId() {
        if($this->_sUserBillCountryId === null) {
            $oUser = $this->getUser();
            $this->_sUserBillCountryId = $oUser->oxuser__oxcountryid->value;
        }
        return $this->_sUserBillCountryId;
    }

    /**
     * Get, and set if needed, the delivery country id if existant
     * 
     * @return string
     */
    protected function getUserDelCountryId() {
        if($this->_sUserDelCountryId === null) {
            $oOrder = oxNew( 'oxorder' );
            $oDelAddress = $oOrder->getDelAddressInfo();
            $sUserDelCountryId = false;
            if($oDelAddress !== null) {
                $sUserDelCountryId = $oDelAddress->oxaddress__oxcountryid->value;
            }
            $this->_sUserDelCountryId = $sUserDelCountryId;
        }
        return $this->_sUserDelCountryId;
    }

    /**
     * Check if the user is allowed to use the given payment method
     * 
     * @param string $sSubPaymentId ID of the sub payment method ( Visa, MC, etc. )
     * @param string $sType payment type PAYONE
     * 
     * @return bool
     */
    protected function isPaymentMethodAvailableToUser($sSubPaymentId, $sType) {
        if(array_key_exists($sSubPaymentId.'_'.$sType, $this->_aCheckedSubPayments) === false) {
            $sBaseQuery = "SELECT COUNT(*) FROM fcpopayment2country WHERE fcpo_paymentid = '{$sSubPaymentId}' AND fcpo_type = '{$sType}'";
            $sUserBillCountryId = $this->getUserBillCountryId();
            $sUserDelCountryId = $this->getUserDelCountryId();
            if($sUserDelCountryId !== false && $sUserBillCountryId != $sUserDelCountryId) {
                $sWhereCountry = "AND (fcpo_countryid = '{$sUserBillCountryId}' || fcpo_countryid = '{$sUserDelCountryId}')";
            } else {
                $sWhereCountry = "AND fcpo_countryid = '{$sUserBillCountryId}'";
            }
            $sQuery = "SELECT IF(({$sBaseQuery} LIMIT 1) > 0,IF(({$sBaseQuery} {$sWhereCountry} LIMIT 1) > 0,1,0),1)";
            $this->_aCheckedSubPayments[$sSubPaymentId.'_'.$sType] = oxDb::getDb()->GetOne($sQuery);
        }
        return $this->_aCheckedSubPayments[$sSubPaymentId.'_'.$sType];
    }

    /**
     * Check if there are available sub payment types for the user
     * 
     * @param string $sType payment type PAYONE
     * 
     * @return bool
     */
    public function hasPaymentMethodAvailableSubTypes($sType) {
        if($sType == 'cc') {
            if($this->getVisa()) return true;
            if($this->getMastercard()) return true;
            if($this->getAmex()) return true;
            if($this->getDiners()) return true;
            if($this->getJCB()) return true;
            if($this->getMaestroInternational()) return true;
            if($this->getMaestroUK()) return true;
            if($this->getDiscover()) return true;
            if($this->getCarteBleue()) return true;
        } elseif($sType == 'sb') {
            if($this->getSofortUeberweisung()) return true;
            if($this->getGiropay()) return true;
            if($this->getEPS()) return true;
            if($this->getPostFinanceEFinance()) return true;
            if($this->getPostFinanceCard()) return true;
            if($this->getIdeal()) return true;
        }
        return false;
    }

    /**
     * Check if sub payment method Visa is available to the user
     * 
     * @return bool
     */
    public function getVisa() {
        return ($this->getConfigParam('blFCPOVisaActivated') && $this->isPaymentMethodAvailableToUser('V', 'cc'));
    }

    /**
     * Check if sub payment method Mastercard is available to the user
     * 
     * @return bool
     */
    public function getMastercard() {
        return ($this->getConfigParam('blFCPOMastercardActivated') && $this->isPaymentMethodAvailableToUser('M', 'cc'));
    }

    /**
     * Check if sub payment method Amex is available to the user
     * 
     * @return bool
     */
    public function getAmex() {
        return ($this->getConfigParam('blFCPOAmexActivated') && $this->isPaymentMethodAvailableToUser('A', 'cc'));
    }

    /**
     * Check if sub payment method Diners is available to the user
     * 
     * @return bool
     */
    public function getDiners() {
        return ($this->getConfigParam('blFCPODinersActivated') && $this->isPaymentMethodAvailableToUser('D', 'cc'));
    }

    /**
     * Check if sub payment method JCB is available to the user
     * 
     * @return bool
     */
    public function getJCB() {
        return ($this->getConfigParam('blFCPOJCBActivated') && $this->isPaymentMethodAvailableToUser('J', 'cc'));
    }

    /**
     * Check if sub payment method MaestroInternational is available to the user
     * 
     * @return bool
     */
    public function getMaestroInternational() {
        return ($this->getConfigParam('blFCPOMaestroIntActivated') && $this->isPaymentMethodAvailableToUser('O', 'cc'));
    }

    /**
     * Check if sub payment method MaestroUK is available to the user
     * 
     * @return bool
     */
    public function getMaestroUK() {
        return ($this->getConfigParam('blFCPOMaestroUKActivated') && $this->isPaymentMethodAvailableToUser('U', 'cc'));
    }

    /**
     * Check if sub payment method Discover is available to the user
     * 
     * @return bool
     */
    public function getDiscover() {
        return ($this->getConfigParam('blFCPODiscoverActivated') && $this->isPaymentMethodAvailableToUser('C', 'cc'));
    }

    /**
     * Check if sub payment method CarteBleue is available to the user
     * 
     * @return bool
     */
    public function getCarteBleue() {
        return ($this->getConfigParam('blFCPOCarteBleueActivated') && $this->isPaymentMethodAvailableToUser('B', 'cc'));
    }

    /**
     * Check if sub payment method SofortUeberweisung is available to the user
     * 
     * @return bool
     */
    public function getSofortUeberweisung() {
        return ($this->getConfigParam('blFCPOSofoActivated') && $this->isPaymentMethodAvailableToUser('PNT', 'sb'));
    }

    /**
     * Check if sub payment method Giropay is available to the user
     * 
     * @return bool
     */
    public function getGiropay() {
        return ($this->getConfigParam('blFCPOgiroActivated') && $this->isPaymentMethodAvailableToUser('GPY', 'sb'));
    }

    /**
     * Check if sub payment method EPS is available to the user
     * 
     * @return bool
     */
    public function getEPS() {
        return ($this->getConfigParam('blFCPOepsActivated') && $this->isPaymentMethodAvailableToUser('EPS', 'sb'));
    }

    /**
     * Check if sub payment method PostFinanceEFinance is available to the user
     * 
     * @return bool
     */
    public function getPostFinanceEFinance() {
        return ($this->getConfigParam('blFCPOPoFiEFActivated') && $this->isPaymentMethodAvailableToUser('PFF', 'sb'));
    }

    /**
     * Check if sub payment method PostFinanceCard is available to the user
     * 
     * @return bool
     */
    public function getPostFinanceCard() {
        return ($this->getConfigParam('blFCPOPoFiCaActivated') && $this->isPaymentMethodAvailableToUser('PFC', 'sb'));
    }

    /**
     * Check if sub payment method Ideal is available to the user
     * 
     * @return bool
     */
    public function getIdeal() {
        return ($this->getConfigParam('blFCPOiDealActivated') && $this->isPaymentMethodAvailableToUser('IDL', 'sb'));
    }

    /**
     * Get encoding of the shop
     * 
     * @return string
     */
    public function getEncoding() {
        $oConfig = $this->getConfig();
        if($oConfig->isUtf()) {
            return 'UTF-8';
        }
        return 'ISO-8859-1';
    }

    /**
     * Get the basket brut price in the smallest unit of the currency
     * 
     * @return int
     */
    public function getAmount() {
        $oBasket = $this->getSession()->getBasket();
        return number_format($oBasket->getPrice()->getBruttoPrice(), 2, '.', '')*100;
    }

    /**
     * Get the language the user is using in the shop
     * 
     * @return string
     */
    public function getTplLang() {
        return oxLang::getInstance()->getLanguageAbbr();
    }
    
    /*
     * Return language id
     * 
     * @return int
     */
    public function fcGetLangId() {
        $oLang = oxLang::getInstance();
        $iLang = ( $iLang === null && $blAdmin ) ? $oLang->getTplLanguage() : $iLang;
        if ( !isset( $iLang ) ) {
            $iLang = $oLang->getBaseLanguage();
            if ( !isset( $iLang ) ) {
                $iLang = 0;
            }
        }
        return $iLang;
    }

    /**
     * Get configured operation mode ( live or test ) for creditcard
     * 
     * @param string $sType sub payment type PAYONE
     * 
     * @return string
     */
    protected function _getOperationModeCC($sType = '') {
        $oPayment = oxNew('oxpayment');
        $oPayment->load('fcpocreditcard');
        return $oPayment->fcpoGetOperationMode($sType);
    }

    /**
     * Get verification safety hash for creditcard payment method
     * 
     * @return string
     */
    public function getHashCC($sType = '') {
        $sHash = md5(
            $this->getSubAccountId().
            $this->getEncoding().
            $this->getMerchantId().
            $this->_getOperationModeCC($sType).
            $this->getPortalId().
            'creditcardcheck'.
            'JSON'.
            'yes'.
            $this->getPortalKey()
        );
        return $sHash;
    }

    /**
     * Get configured operation mode ( live or test ) for debitnote payment method
     * 
     * @return string
     */
    protected function _getOperationModeELV() {
        $oPayment = oxNew('oxpayment');
        $oPayment->load('fcpodebitnote');
        return $oPayment->fcpoGetOperationMode();
    }

    /**
     * Get verification safety hash for debitnote payment method with checktype parameter
     * 
     * @return string
     */
    public function getHashELVWithChecktype() {
        $sHash = md5(
            $this->getSubAccountId().
            $this->getChecktype().
            $this->getEncoding().
            $this->getMerchantId().
            $this->_getOperationModeELV().
            $this->getPortalId().
            'bankaccountcheck'.
            'JSON'.
            $this->getPortalKey()
        );
        return $sHash;
    }

    /**
     * Get verification safety hash for debitnote payment method without checktype parameter
     * 
     * @return string
     */
    public function getHashELVWithoutChecktype() {
        $sHash = md5(
            $this->getSubAccountId().
            $this->getEncoding().
            $this->getMerchantId().
            $this->_getOperationModeELV().
            $this->getPortalId().
            'bankaccountcheck'.
            'JSON'.
            $this->getPortalKey()
        );
        return $sHash;
    }

    /**
     * Extends oxid standard method getPaymentList
     * Extends it with the creditworthiness check for the user
     * 
     * @return string
	 * @extend  getPaymentList
     */
    public function getPaymentList() {
        if ( $this->_oPaymentList === null ) {
            $oUser = $this->getUser();
            $blContinue = false;
            if($oUser && oxConfig::getInstance()->getConfigParam('sFCPOBonicheckMoment') != 'after') {
                $blContinue = $oUser->checkAddressAndScore();
            } else {
                $blContinue = $oUser->checkAddressAndScore(true, false);
            }
            if($blContinue === true) {
                return parent::getPaymentList();
            } else {
                oxUtils::getInstance()->redirect( $this->getConfig()->getShopHomeURL() .'cl=user', false );
            }
        }
        return $this->_oPaymentList;
    }
    
    /**
     * Extends oxid standard method validatePayment
     * Extends it with the creditworthiness check for the user
     * 
     * Validates oxidcreditcard and oxiddebitnote user payment data.
     * Returns null if problems on validating occured. If everything
     * is OK - returns "order" and redirects to payment confirmation
     * page.
     *
     * Session variables:
     * <b>paymentid</b>, <b>dynvalue</b>, <b>payerror</b>
     *
     * @return  mixed
     */
    public function validatePayment() {
        $sReturn = parent::validatePayment();
        if($sReturn == 'order' && oxConfig::getInstance()->getConfigParam('sFCPOBonicheckMoment') == 'after') { // success
            $oSession = $this->getSession();
            $oUser = $this->getUser();
            $blContinue = false;
            if($oUser) {
                if (! ($sPaymentId = oxConfig::getParameter( 'paymentid' ))) {
                    $sPaymentId = oxSession::getVar('paymentid');
                }
                
                $oPayment = oxNew( 'oxpayment' );
                $oPayment->load( $sPaymentId );
                
                $blApproval = true;
                $aApproval = oxConfig::getParameter('fcpo_bonicheckapproved');
                if(array_key_exists($sPaymentId, $aApproval) && $aApproval[$sPaymentId] == 'false') {
                    $blApproval = false;
                }
                
                if($oPayment->fcBoniCheckNeeded() && $blApproval === true) {
                    $blContinue = $oUser->checkAddressAndScore(false);
                    if($oUser->oxuser__oxboni->value < $oPayment->oxpayments__oxfromboni->value) {
                        $blContinue = false;
                    }
                } else {
                    $blContinue = true;
                }
            }
            if($blContinue === true) {
                return $sReturn;
            } else {
                $iLangId = $this->fcGetLangId();
                
                #$oSession->setVariable( 'payerror', $oPayment->getPaymentErrorNumber() );
                oxSession::setVar( 'payerror', -20 );
                oxSession::setVar( 'payerrortext', oxConfig::getInstance()->getConfigParam('sFCPODenialText_'.$iLangId));

                //#1308C - delete paymentid from session, and save selected it just for view
                oxSession::deleteVar( 'paymentid' );
                if (! ($sPaymentId = oxConfig::getParameter( 'paymentid' ))) {
                    $sPaymentId = oxSession::getVar('paymentid');
                }
                oxSession::setVar( '_selected_paymentid', $sPaymentId );
                oxSession::deleteVar( 'stsprotection' );
                if($this->_fcGetCurrentVersion() >= 4400) {
                    $oBasket = $oSession->getBasket();
                    $oBasket->setTsProductId(null);
                }
                return;
            }
        }
        return $sReturn;
    }
    
    public function fcGetApprovalText() {
        $iLangId = $this->fcGetLangId();
        return oxConfig::getInstance()->getConfigParam('sFCPOApprovalText_'.$iLangId);
    }
    
    public function fcShowApprovalMessage() {
        if(oxConfig::getInstance()->getConfigParam('sFCPOBonicheckMoment') == 'after') {
            return true;
        }
        return false;
    }

    /**
     * Loads shop version and formats it in a certain way
     *
     * @return string
     */
    public function getIntegratorid() {
        $sEdition = $this->getConfig()->getActiveShop()->oxshops__oxedition->value;
        if($sEdition == 'CE') {
            return '2027000';
        } elseif($sEdition == 'PE') {
            return '2028000';
        } elseif($sEdition == 'EE') {
            return '2029000';
        }
        return '';
    }

    /**
     * Loads shop edition and shop version and formats it in a certain way
     *
     * @return string
     */
    public function getIntegratorver() {
        $oConfig = $this->getConfig();
        return $oConfig->getActiveShop()->oxshops__oxedition->value.$oConfig->getActiveShop()->oxshops__oxversion->value;
    }

    /**
     * get PAYONE module version
     *
     * @return string
     */
    public function getIntegratorextver() {
        return fcpoRequest::getVersion();
    }
    
    /**
     * Get user payment by payment id with oxid bugfix for getting last payment
     *
     * @param oxUser $oUser        user object
     * @param string $sPaymentType payment type
     *
     * @return bool
     */
    protected function _fcGetPaymentByPaymentType( $oUser = null, $sPaymentType = null ) {
        $blGet = false;
        if ( $oUser && $sPaymentType != null ) {
            $oUserPayment = oxNew( 'oxuserpayment');
            $oDb = oxDb::getDb();
            $sQ  = 'select oxpaymentid from oxorder where oxpaymenttype=' . $oDb->quote( $sPaymentType ) . ' and
                    oxuserid=' . $oDb->quote( $oUser->getId() ).' order by oxorderdate desc';
            if ( ( $sOxId = $oDb->getOne( $sQ ) ) ) {
                $blGet = $oUserPayment->load( $sOxId );
            }
            
            return $oUserPayment;
        }

        return false;
    }
    
    /**
     * Assign debit note payment values to view data. Loads user debit note payment
     * if available and assigns payment data to $this->_aDynValue
     *
     * @return null
     */
    protected function _assignDebitNoteParams() {
        parent::_assignDebitNoteParams();
        if((bool)$this->getConfigParam('sFCPOSaveBankdata') === true) {
            //such info available ?
            if ( $oUserPayment = $this->_fcGetPaymentByPaymentType( $this->getUser(), 'fcpodebitnote' ) ) {
                $aAddPaymentData = oxUtils::getInstance()->assignValuesFromText( $oUserPayment->oxuserpayments__oxvalue->value );
                //checking if some of values is allready set in session - leave it
                foreach ( $aAddPaymentData as $oData ) {
                    if ( !isset( $this->_aDynValue[$oData->name] ) ||
                       (  isset( $this->_aDynValue[$oData->name] ) && !$this->_aDynValue[$oData->name] ) ) {
                        $this->_aDynValue[$oData->name] = $oData->value;
                    }
                }
            }
        }
    }
    
    /**
     * Template variable getter. Returns dyn values
     *
     * @return array
     */
    public function getDynValue() {
        $aReturn = parent::getDynValue();
        if((bool)$this->getConfigParam('sFCPOSaveBankdata') === true) {
            $aPaymentList = $this->getPaymentList();
            if ( isset( $aPaymentList['fcpodebitnote'] ) ) {
                $this->_assignDebitNoteParams();
            }
        }
        return $this->_aDynValue;
    }
    
    /**
     * Return ISO2 code of bill country
     * 
     * @return string
     */
    public function fcGetBillCountry() {
        $sBillCountryId = $this->getUserBillCountryId();
        $oCountry = oxNew('oxcountry');
        if($oCountry->load($sBillCountryId)) {
            return $oCountry->oxcountry__oxisoalpha2->value;
        }
        return '';
    }
    
    /**
     * Extends oxid standard method _setValues
     * Extends it with the approval checkbox in the longdesc property
     * 
     * Calculate payment cost for each payment. Sould be removed later
     *
     * @param array    &$aPaymentList payments array
     * @param oxBasket $oBasket       basket object
     *
     * @return null
     */
    protected function _setValues( & $aPaymentList, $oBasket = null ) {
        parent::_setValues($aPaymentList, $oBasket);
        if ( is_array($aPaymentList) ) {
            foreach ( $aPaymentList as $oPayment ) {
                if($oPayment->fcIsPayOnePaymentType() && $this->fcShowApprovalMessage() && $oPayment->fcBoniCheckNeeded()) {
                    $sApprovalLongdesc = '<br><table><tr><td><input type="hidden" name="fcpo_bonicheckapproved['.$oPayment->getId().']" value="false"><input type="checkbox" name="fcpo_bonicheckapproved['.$oPayment->getId().']" value="true" style="margin-bottom:0px;margin-right:10px;"></td><td>'.$this->fcGetApprovalText().'</td></tr></table>';
                    $oPayment->oxpayments__oxlongdesc->value .= $sApprovalLongdesc;
                }
            }
        }
    }

    /**
     * Get current version number as 4 digit integer e.g. Oxid 4.5.9 is 4590
     * 
     * @return integer
     */
    protected function _fcGetCurrentVersion() {
        $sVersion = oxConfig::getInstance()->getActiveShop()->oxshops__oxversion->value;
        $iVersion = (int)str_replace('.', '', $sVersion);
        while ($iVersion < 1000) {
            $iVersion = $iVersion*10;
        }
        return $iVersion;
    }
    
    /**
     * Extends oxid standard method _setDeprecatedValues
     * Extends it with the approval checkbox in the longdesc property
     * 
     * Calculate payment cost for each payment. Sould be removed later
     *
     * @param array    &$aPaymentList payments array
     * @param oxBasket $oBasket       basket object
     *
     * @return null
     */
    protected function _setDeprecatedValues( & $aPaymentList, $oBasket = null ) {
        parent::_setDeprecatedValues($aPaymentList, $oBasket);
        if($this->_fcGetCurrentVersion() <= 4700) {
            if ( is_array($aPaymentList) ) {
                $oLang = oxLang::getInstance();
                foreach ( $aPaymentList as $oPayment ) {
                    if($oPayment->fcIsPayOnePaymentType() && $this->fcShowApprovalMessage() && $oPayment->fcBoniCheckNeeded()) {
                        $sApprovalLongdesc = '<br><table><tr><td><input type="hidden" name="fcpo_bonicheckapproved['.$oPayment->getId().']" value="false"><input type="checkbox" name="fcpo_bonicheckapproved['.$oPayment->getId().']" value="true" style="margin-bottom:0px;margin-right:10px;"></td><td>'.$this->fcGetApprovalText().'</td></tr></table>';
                        $oPayment->oxpayments__oxlongdesc->value .= $sApprovalLongdesc;
                    }
                }
            }
        }
    }

}