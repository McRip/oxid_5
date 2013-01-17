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
     * Overrides standard oxid save method
     * 
     * Saves order article object. If saving succeded - updates
     * article stock information if oxOrderArticle::isNewOrderItem()
     * returns TRUE. Returns saving status
     *
     * @return bool
     */
    public function save($oOrder = false) {
        
        $blPresaveOrder = (bool)$this->getConfig()->getConfigParam('blFCPOPresaveOrder');
        if($oOrder === false || $blPresaveOrder === false || $oOrder->isPayOnePaymentType() === false) {
            return parent::save();
        }
        
        $blReduceStockBefore = !(bool)$this->getConfig()->getConfigParam('blFCPOReduceStock');       
        if(oxConfig::getParameter('fcposuccess') && oxConfig::getParameter('refnr')) {
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

            // seting downloadable products article files
            $this->_setOrderFiles();

            // marking object as "non new" disable further stock changes
            $this->setIsNewOrderItem( false );
        }

        return $blSave;
    }

}