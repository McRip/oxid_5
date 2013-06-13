<?php

/**
 * Module for core class oxorderarticle
 * Handles all the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend oxorderarticle
 */
class fcPayOneOrderarticle extends fcPayOneOrderarticle_parent {

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
     * Overrides standard oxid save method
     * 
     * Saves order article object. If saving succeded - updates
     * article stock information if oxOrderArticle::isNewOrderItem()
     * returns TRUE. Returns saving status
     *
     * @return bool
     */
    public function save($oOrder = false, $blFinishingSave = true) {
        $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
        if($oOrder === false || $blPresaveOrder === false || $oOrder->isPayOnePaymentType() === false) {
            return parent::save();
        }
        
        $blReduceStockBefore = !(bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');
        if(oxConfig::getParameter('fcposuccess') && oxConfig::getParameter('refnr') || ($blFinishingSave === true && $blPresaveOrder === true && $blReduceStockBefore === false)) {
            $blBefore = false;
        } else {
            $blBefore = true;
        }
        
        // ordered articles
        if ( $blBefore === false || ( $blSave = oxBase::save() ) && $this->isNewOrderItem() ) {
            $myConfig = $this->getConfig();
            if ( $myConfig->getConfigParam( 'blUseStock' ) ) {
                if ($myConfig->getConfigParam( 'blPsBasketReservationEnabled' )) {
                    $this->getSession()
                            ->getBasketReservations()
                            ->commitArticleReservation(
                                   $this->oxorderarticles__oxartid->value,
                                   $this->oxorderarticles__oxamount->value
                           );
                } else {
                    if(($blReduceStockBefore == true && $blBefore == true) || ($blReduceStockBefore == false && $blBefore == false)) {
                        $this->updateArticleStock( $this->oxorderarticles__oxamount->value * (-1), $myConfig->getConfigParam( 'blAllowNegativeStock' ) );
                    }
                }
            }

            if($this->_fcGetCurrentVersion() >= 4600) {
                // seting downloadable products article files
                $this->_setOrderFiles();
            }

            // marking object as "non new" disable further stock changes
            $this->setIsNewOrderItem( false );
        }

        return $blSave;
    }
    
    /**
     * Deletes order article object. If deletion succeded - updates
     * article stock information. Returns deletion status
     *
     * @param string $sOXID Article id
     *
     * @return bool
     */
    public function delete( $sOXID = null) {
        $sPaymentId = oxSession::getInstance()->getBasket()->getPaymentId();
        if($sPaymentId) {
            $oPayment = oxNew('oxpayment');
            $oPayment->load($sPaymentId);
            if($oPayment->fcIsPayOnePaymentType() === false) {
                return parent::delete($sOXID);
            }
        }
        if ( $blDelete = oxBase::delete( $sOXID ) ) {
            $myConfig = $this->getConfig();
            $blReduceStockBefore = !(bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');
            if ( $this->oxorderarticles__oxstorno->value != 1 && $blReduceStockBefore !== false ) {
                $this->updateArticleStock( $this->oxorderarticles__oxamount->value, $myConfig->getConfigParam('blAllowNegativeStock') );
            }
        }
        return $blDelete;
    }

}