<?php

/**
 * Module for core class oxbasketitem
 * Handles all the needed extra functionality for the PAYONE payment methods
 *
 * @author FATCHIP GmbH | Robert Müller
 * @extend oxbasketitem
 */
class fcPayOneBasketitem extends fcPayOneBasketitem_parent {

    /**
     * Overrides standard oxid getArticle method
     * 
     * Retrieves the article .Throws an execption if article does not exist,
     * is not buyable or visible.
     *
     * @param bool   $blCheckProduct       checks if product is buyable and visible
     * @param string $sProductId           product id
     * @param bool   $blDisableLazyLoading disable lazy loading
     *
     * @throws oxArticleException, oxNoArticleException exception
     *
     * @return oxarticle
     */
    public function getArticle( $blCheckProduct = true, $sProductId = null, $blDisableLazyLoading = false ) {
        $blReduceStockBefore = !(bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');
        if($blReduceStockBefore && oxConfig::getParameter('fcposuccess') && oxConfig::getParameter('refnr')) {
            $blCheckProduct = false;
        }
        try {
            $blReturn = parent::getArticle($blCheckProduct, $sProductId, $blDisableLazyLoading);
        } catch (Exception $exc) {
            throw $exc;
        }
        return $blReturn;
    }
    
    /**
     * Overrides standard oxid save method
     * 
     * Saves order article object. If saving succeded - updates
     * article stock information if oxOrderArticle::isNewOrderItem()
     * returns TRUE. Returns saving status
     *
     * @return bool

    public function save($oOrder = false) {
        
        $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
        if($oOrder === false || $blPresaveOrder === false || $oOrder->isPayOnePaymentType() === false) {
            return parent::save();
        }
        
        // ordered articles
        if ( ( $blSave = oxBase::save() ) && $this->isNewOrderItem() ) {
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
                    $blReduceStockBefore = (bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');                    
                    if(oxConfig::getParameter('fcposuccess') && oxConfig::getParameter('refnr') && oxSession::getVar('fcpoTxid')) {
                        $blBefore = false;
                    } else {
                        $blBefore = true;
                    }                    
                    #if(($blReduceStockBefore == true && $blBefore == true) || ($blReduceStockBefore == false && $blBefore == false)) {
                        $this->updateArticleStock( $this->oxorderarticles__oxamount->value * (-1), $myConfig->getConfigParam( 'blAllowNegativeStock' ) );
                    #}
                }
            }

            // seting downloadable products article files
            $this->_setOrderFiles();

            // marking object as "non new" disable further stock changes
            $this->setIsNewOrderItem( false );
        }

        return $blSave;
    }     */

}