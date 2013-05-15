<?php

/**
 * Module for core class oxorder
 * Handles all the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend oxorder
 */
class fcPayOneOrder extends fcPayOneOrder_parent {

    /*
     * Array with all reponse paramaters from the API order request
     *
     * @var array
     */
    protected $_aResponse = null;

    protected $_blIsRedirectAfterSave = null;
    
    protected $_blIsPayonePayment = false;


    /**
     * Checks if the selected payment method for this order is a PAYONE payment method
     *
     * @param string $sPaymenttype payment id. Default is null
     * 
     * @return bool
     */
    public function isPayOnePaymentType($sPaymenttype = null) {
        if(!$sPaymenttype) {
            $sPaymenttype = $this->oxorder__oxpaymenttype->value;
        }
        $aTypes = fcPayOnePayment::fcGetPayonePaymentTypes();
        if(array_search($sPaymenttype, $aTypes) === false) {
            return false;
        }
        return true;
    }

    /**
     * Removes MSIE(\s)?(\S)*(\s) from browser agent information
     *
     * @param string $sAgent browser user agent idenfitier
     *
     * @return string
     */
    protected function _fcProcessUserAgentInfo( $sAgent )
    {
        if ( $sAgent ) {
            $sAgent = getStr()->preg_replace( "/MSIE(\s)?(\S)*(\s)/", "", (string) $sAgent );
        }
        return $sAgent;
    }
    
    /**
     * Compares the HTTP user agent before and after the redirect payment method.
     * If HTTP user agent is diffenrent it checks if the remote tokens match.
     * If so, the current user agent is updated in the user session.
     * 
     * @return null
     */
    protected function _fcpoCheckUserAgent() {
        $oSession = oxSession::getInstance();
        $oUtils = oxUtilsServer::getInstance();

        $sAgent = $oUtils->getServerVar( 'HTTP_USER_AGENT' );
        $sExistingAgent = $oSession->getVar('sessionagent');
        $sAgent = $this->_fcProcessUserAgentInfo( $sAgent );
        $sExistingAgent = $this->_fcProcessUserAgentInfo( $sExistingAgent );
        
        if ($this->_fcGetCurrentVersion() >= 4310 && $sAgent && $sAgent !== $sExistingAgent ) {
            $sInputToken = oxConfig::getInstance()->getParameter('rtoken');
            $sToken = $oSession->getRemoteAccessToken(false);
            $blTokenEqual = !(bool)strcmp($sInputToken, $sToken);
            $blValid = $sInputToken && $blTokenEqual;
            if($blValid === true) {
                $oSession->setVar( "sessionagent", $oUtils->getServerVar( 'HTTP_USER_AGENT' ) );
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
     * Returns true if this request is the return to the shop from a payment provider where the user has been redirected to
     * 
     * @return bool
     */
    protected function _isRedirectAfterSave() {
        if($this->_blIsRedirectAfterSave === null) {
            $this->_blIsRedirectAfterSave = false;
            if(oxConfig::getParameter('fcposuccess') && oxConfig::getParameter('refnr') && oxSession::getVar('fcpoTxid')) {
                $this->_blIsRedirectAfterSave = true;
            }
        }
        return $this->_blIsRedirectAfterSave;
    }


    /**
     * Overrides standard oxid finalizeOrder method
     * 
     * Order checking, processing and saving method.
     * Before saving performed checking if order is still not executed (checks in
     * database oxorder table for order with know ID), if yes - returns error code 3,
     * if not - loads payment data, assigns all info from basket to new oxorder object
     * and saves full order with error status. Then executes payment. On failure -
     * deletes order and returns error code 2. On success - saves order (oxorder::save()),
     * removes article from wishlist (oxorder::_updateWishlist()), updates voucher data
     * (oxorder::_markVouchers()). Finally sends order confirmation email to customer
     * (oxemail::SendOrderEMailToUser()) and shop owner (oxemail::SendOrderEMailToOwner()).
     * If this is order racalculation, skipping payment execution, marking vouchers as used
     * and sending order by email to shop owner and user
     * Mailing status (1 if OK, 0 on error) is returned.
     *
     * @param oxBasket $oBasket              Shopping basket object
     * @param object   $oUser                Current user object
     * @param bool     $blRecalculatingOrder Order recalculation
     *
     * @return integer
     * @extend finalizeOrder
     */
    public function finalizeOrder( oxBasket $oBasket, $oUser, $blRecalculatingOrder = false ) {

        // Use standard method if payment type does not belong to PAYONE
        if($this->isPayOnePaymentType($oBasket->getPaymentId()) === false) {
            return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
        }

        $blSaveAfterRedirect = $this->_isRedirectAfterSave();

        // check if this order is already stored
        $sGetChallenge = oxSession::getVar( 'sess_challenge' );
        if ( $blSaveAfterRedirect === false && $this->_checkOrderExist( $sGetChallenge ) ) {
            oxUtils::getInstance()->logger( 'BLOCKER' );
            // we might use this later, this means that somebody klicked like mad on order button
            return self::ORDER_STATE_ORDEREXISTS;
        }

        // if not recalculating order, use sess_challenge id, else leave old order id
        if ( !$blRecalculatingOrder ) {
            // use this ID
            $this->setId( $sGetChallenge );

            // validating various order/basket parameters before finalizing
            if ( $iOrderState = $this->validateOrder( $oBasket, $oUser ) ) {
                return $iOrderState;
            }
        }

        // copies user info
        $this->_setUser( $oUser );

        // copies basket info
        $this->_loadFromBasket( $oBasket );

        // payment information
        $oUserPayment = $this->_setPayment( $oBasket->getPaymentId() );

        // set folder information, if order is new
        // #M575 in recalcualting order case folder must be the same as it was
        if ( !$blRecalculatingOrder ) {
            $this->_setFolder();
        }
        if($blSaveAfterRedirect === true) {
            $iSessRefNr = oxSession::getVar('fcpoRefNr');
            if(oxConfig::getParameter('refnr') != $iSessRefNr) {
                $oLang = oxLang::getInstance();
                oxSession::deleteVar('fcpoRefNr');
                return $oLang->translateString('FCPO_MANIPULATION');
            }
            
            $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
            if($blPresaveOrder === true) {
                $this->oxorder__oxordernr = new oxField(oxSession::getVar('fcpoOrderNr'), oxField::T_RAW);
            }
            $this->oxorder__fcpotxid = new oxField(oxSession::getVar('fcpoTxid'), oxField::T_RAW);
            $this->oxorder__fcporefnr = new oxField(oxConfig::getParameter('refnr'), oxField::T_RAW);
            $this->oxorder__fcpoauthmode = new oxField(oxSession::getVar('fcpoAuthMode'), oxField::T_RAW);
            $this->oxorder__fcpomode = new oxField(oxSession::getVar('fcpoMode'), oxField::T_RAW);
            oxDb::getDb()->Execute("UPDATE fcporefnr SET fcpo_txid = '".oxSession::getVar('fcpoTxid')."' WHERE fcpo_refnr = '".oxConfig::getParameter('refnr')."'");
            oxSession::deleteVar('fcpoOrderNr');
            oxSession::deleteVar('fcpoTxid');
            oxSession::deleteVar('fcpoRefNr');
            oxSession::deleteVar('fcpoAuthMode');
            $this->_fcpoCheckUserAgent();
        } else {
            // executing payment (on failure deletes order and returns error code)
            // in case when recalcualting order, payment execution is skipped
            if ( !$blRecalculatingOrder ) {
                $blRet = $this->_executePayment( $oBasket, $oUserPayment );
                if ( $blRet !== true ) {
                    return $blRet;
                }
            }
        }

        //saving all order data to DB
        $this->save();

        if($blSaveAfterRedirect === true) {
            oxDb::getDb()->Execute("UPDATE fcpotransactionstatus SET fcpo_ordernr = '{$this->oxorder__oxordernr->value}' WHERE fcpo_txid = '".oxSession::getVar('fcpoTxid')."'");
        }

        if($this->_fcGetCurrentVersion() >= 4399) {
            // executing TS protection
            if ( !$blRecalculatingOrder && $oBasket->getTsProductId()) {
                $blRet = $this->_executeTsProtection( $oBasket );
                if ( $blRet !== true ) {
                    return $blRet;
                }
            }
        }

        // deleting remark info only when order is finished
        oxSession::deleteVar( 'ordrem' );
        oxSession::deleteVar( 'stsprotection' );

        //#4005: Order creation time is not updated when order processing is complete
        if ( method_exists($this, '_updateOrderDate') && !$blRecalculatingOrder ) {
           $this->_updateOrderDate();
        }
        
        // updating order trans status (success status)
        $this->_setOrderStatus( 'OK' );

        // store orderid
        $oBasket->setOrderId( $this->getId() );

        // updating wish lists
        $this->_updateWishlist( $oBasket->getContents(), $oUser );

        // updating users notice list
        $this->_updateNoticeList( $oBasket->getContents(), $oUser );

        // marking vouchers as used and sets them to $this->_aVoucherList (will be used in order email)
        // skipping this action in case of order recalculation
        if ( !$blRecalculatingOrder ) {
            $this->_markVouchers( $oBasket, $oUser );
        }

        // send order by email to shop owner and current user
        // skipping this action in case of order recalculation
        if ( !$blRecalculatingOrder ) {
            $iRet = $this->_sendOrderByEmail( $oUser, $oBasket, $oUserPayment );
        } else {
            $iRet = self::ORDER_STATE_OK;
        }

        return $iRet;
    }

    /**
     * Overrides standard oxid _insert method
     * 
     * Inserts order object information in DB. Returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        if($this->_fcGetCurrentVersion() <= 4700) {
            return parent::_insert();
        }

        $myConfig = $this->getConfig();
        $oUtilsDate = oxUtilsDate::getInstance();

        //V #M525 orderdate must be the same as it was
        if ( !$this->oxorder__oxorderdate->value ) {
            $this->oxorder__oxorderdate = new oxField(date( 'Y-m-d H:i:s', $oUtilsDate->getTime() ), oxField::T_RAW);
        } else {
            $this->oxorder__oxorderdate = new oxField( $oUtilsDate->formatDBDate( $this->oxorder__oxorderdate->value, true ));
        }

        $this->oxorder__oxshopid    = new oxField($myConfig->getShopId(), oxField::T_RAW);
        $this->oxorder__oxsenddate  = new oxField( $oUtilsDate->formatDBDate( $this->oxorder__oxsenddate->value, true ));

        if ( ( $blInsert = parent::_insert() ) ) {
            // setting order number
            if ( !$this->oxorder__oxordernr->value ) {
                $blInsert = $this->_setNumber();
            } else {
                oxNew( 'oxCounter' )->update( $this->_getCounterIdent(), $this->oxorder__oxordernr->value );
            }
        }
        return $blInsert;
    }

    /**
     * Overrides standard oxid save method
     * 
     * Save orderarticles only when not already existing
     * 
     * Updates/inserts order object and related info to DB
     *
     * @return null
     */
    public function save() {
        $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
        if($blPresaveOrder === false || $this->isPayOnePaymentType() === false) {
            return parent::save();
        }
        
        if($this->oxorder__oxshopid === false) {
            $this->oxorder__oxshopid = new oxField(oxConfig::getInstance()->getActiveShop()->getId());
        }
        
        if ( ( $blSave = oxBase::save() ) ) {
            $blSaveAfterRedirect = $this->_isRedirectAfterSave();
            
            // saving order articles
            $oOrderArticles = $this->getOrderArticles();
            if ( $oOrderArticles && count( $oOrderArticles ) > 0 ) {
                foreach ( $oOrderArticles as $oOrderArticle ) {
                    $oOrderArticle->save($this);
                }
            }
        }

        return $blSave;
    }
    
    /**
     * Checks based on the transaction status received by PAYONE whether
     * the capture request is available for this order at the moment.
     * 
     * @return bool
     */
    public function allowCapture() {
        if($this->oxorder__fcpoauthmode->value == 'authorization') {
            return false;
        }
        $iCount = oxDb::getDb()->GetOne( "SELECT COUNT(*) FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}'" );
        if($iCount == 0) {
            //There should be at least the preauthorization
            return false;
        }
        return true;
    }

    /**
     * Checks based on the transaction status received by PAYONE whether
     * the debit request is available for this order at the moment.
     * 
     * @return bool
     */
    public function allowDebit() {
        if($this->oxorder__fcpoauthmode->value == 'authorization') {
            return true;
        } else {
            $iCount = oxDb::getDb()->getOne("SELECT COUNT(*) FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}' AND fcpo_txaction = 'capture'");
            if($iCount == 0) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Checks based on the payment method whether
     * the settleaccount checkbox should be shown.
     * 
     * @return bool
     */
    public function allowAccountSettlement() {
        if($this->oxorder__oxpaymenttype->value == 'fcpopayadvance' || $this->oxorder__oxpaymenttype->value == 'fcpoonlineueberweisung') {
            return true;
        }
        return false;
    }

    /**
     * Checks based on the selected payment method for this order whether
     * the users bank data has to be transferred for the debit request.
     * 
     * @return bool
     */
    public function debitNeedsBankData() {
        if( $this->oxorder__oxpaymenttype->value == 'fcpoinvoice' ||
            $this->oxorder__oxpaymenttype->value == 'fcpopayadvance' ||
            $this->oxorder__oxpaymenttype->value == 'fcpocashondel' ||
            $this->oxorder__oxpaymenttype->value == 'fcpoonlineueberweisung') {
            return true;
        }
        return false;
    }

    /**
     * Get the current sequence number of the order
     * 
     * @return int
     */
    public function getSequenceNumber() {
        $iCount = oxDb::getDb()->GetOne( "SELECT MAX(fcpo_sequencenumber) FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}'" );
        if($iCount === null) {
            return 0;
        }
        return $iCount+1;
    }

    /**
     * Get the last transaction status the shop received from PAYONE
     * 
     * @return object
     */
    public function getLastStatus() {
        $sOxid = oxDb::getDb()->GetOne( "SELECT * FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}' ORDER BY fcpo_sequencenumber DESC, fcpo_timestamp DESC" );
        if($sOxid) {
            $oStatus = oxNew('fcpotransactionstatus');
            $oStatus->load($sOxid);
            return $oStatus;
        }
        return false;
    }

    /**
     * Get the API log entry from the (pre)authorization request of this order
     * 
     * @return array
     */
    protected function getResponse() {
        if($this->_aResponse === null) {
            $sOxidRequest = oxDb::getDb()->GetOne("SELECT oxid FROM fcporequestlog WHERE fcpo_refnr = '{$this->oxorder__fcporefnr->value}' AND (fcpo_requesttype = 'preauthorization' OR fcpo_requesttype = 'authorization')");
            if($sOxidRequest) {
                $oRequestLog = oxNew('fcporequestlog');
                $oRequestLog->load($sOxidRequest);
                $aResponse = $oRequestLog->getResponseArray();
                if($aResponse) {
                    $this->_aResponse = $aResponse;
                }
            }
        }
        return $this->_aResponse;
    }

    /**
     * Get a certain parameter out of the response array
     * 
     * @return string
     */
    protected function getResponseParameter($sParameter) {
        $aResponse = $this->getResponse();
        if($aResponse) {
            return $aResponse[$sParameter];
        }
        return '';
    }

    /**
     * Get the bankaccount holder of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBankaccountholder() {
        return $this->getResponseParameter('clearing_bankaccountholder');
    }

    
    /**
     * Get the bankname of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBankname() {
        return $this->getResponseParameter('clearing_bankname');
    }

    /**
     * Get the bankcode of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBankcode() {
        return $this->getResponseParameter('clearing_bankcode');
    }

    /**
     * Get the banknumber of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBanknumber() {
        return $this->getResponseParameter('clearing_bankaccount');
    }

    /**
     * Get the BIC code of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBiccode() {
        return $this->getResponseParameter('clearing_bankbic');
    }

    /**
     * Get the IBAN number of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoIbannumber() {
        return $this->getResponseParameter('clearing_bankiban');
    }
    
    /**
     * Get the capturable amount left
     * Returns order sum if the was no capture before
     * Returns order sum minus prior captures if there were captures before
     * 
     * @return double
     */
    public function getFcpoCapturableAmount() {
        $oTransaction = $this->getLastStatus();
        $dReceivable = 0;
        if($oTransaction !== false) {
            $dReceivable = $oTransaction->fcpotransactionstatus__fcpo_receivable->value;
        }
        return $this->oxorder__oxtotalordersum->value - $dReceivable;
    }
    
    /**
     * Function whitch cheks if article stock is valid.
     * If not displays error and returns false.
     *
     * @param object $oBasket basket object
     *
     * @throws oxOutOfStockException exception
     *
     * @return null
     */
    public function validateStock( $oBasket ) {
        $blReduceStockBefore = !(bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');
        $blCheckProduct = true;
        if($blReduceStockBefore && $this->_isRedirectAfterSave()) {
            $blCheckProduct = false;
        }
        foreach ( $oBasket->getContents() as $key => $oContent ) {
            try {
                $oProd = $oContent->getArticle($blCheckProduct);
            } catch ( oxNoArticleException $oEx ) {
                $oBasket->removeItem( $key );
                throw $oEx;
            } catch ( oxArticleInputException $oEx ) {
                $oBasket->removeItem( $key );
                throw $oEx;
            }

            if($blCheckProduct === true) {
                // check if its still available
                if($this->_fcGetCurrentVersion() < 4300) {
                    $dArtStockAmount = $this->fcGetArtStockInBasket( $oBasket, $oProd->getId(), $key );
                } else {
                    $dArtStockAmount = $oBasket->getArtStockInBasket( $oProd->getId(), $key );
                }
                $iOnStock = $oProd->checkForStock( $oContent->getAmount(), $dArtStockAmount );
                if ( $iOnStock !== true ) {
                    $oEx = oxNew( 'oxOutOfStockException' );
                    $oEx->setMessage( 'EXCEPTION_OUTOFSTOCK_OUTOFSTOCK' );
                    $oEx->setArticleNr( $oProd->oxarticles__oxartnum->value );
                    $oEx->setProductId( $oProd->getId() );

                    if (!is_numeric($iOnStock)) {
                        $iOnStock = 0;
                    }
                    $oEx->setRemainingAmount( $iOnStock );
                    throw $oEx;
                }
            }
        }
    }
    
    /**
     * Returns stock of article in basket, including bundle article
     *
     * @param object $oBasket       basket object
     * @param string $sArtId        article id
     * @param string $sExpiredArtId item id of updated article
     *
     * @return double
     */
    public function fcGetArtStockInBasket( $oBasket, $sArtId, $sExpiredArtId = null ) {
        $dArtStock = 0;
        
        $aContents = $oBasket->getContents();
        foreach ( $aContents as $sItemKey => $oOrderArticle ) {
            if ( $oOrderArticle && ( $sExpiredArtId == null || $sExpiredArtId != $sItemKey ) ) {
                if ( $oOrderArticle->getArticle( true )->getId() == $sArtId ) {
                    $dArtStock += $oOrderArticle->getAmount();
                }
            }
        }

        return $dArtStock;
    }

}