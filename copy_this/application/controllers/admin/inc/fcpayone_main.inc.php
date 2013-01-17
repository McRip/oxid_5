<?php

$aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',     'oxcountry', 1, 1, 0 ),
                                        array( 'oxisoalpha2', 'oxcountry', 1, 0, 0 ),
                                        array( 'oxisoalpha3', 'oxcountry', 0, 0, 0 ),
                                        array( 'oxunnum3',    'oxcountry', 0, 0, 0 ),
                                        array( 'oxid',        'oxcountry', 0, 0, 1 )
                                        ),
                     'container2' => array(
                                        array( 'oxtitle',     'oxcountry', 1, 1, 0 ),
                                        array( 'oxisoalpha2', 'oxcountry', 1, 0, 0 ),
                                        array( 'oxisoalpha3', 'oxcountry', 0, 0, 0 ),
                                        array( 'oxunnum3',    'oxcountry', 0, 0, 0 ),
                                        array( 'oxid', 'fcpopayment2country', 0, 0, 1 )
                                        )
                    );
/**
 * Class manages payment countries
 * 
 * @author FATCHIP GmbH | Robert Müller
 */
class ajaxComponent extends ajaxListComponent {
    
    /**
     * Returns SQL query for data to fetch
     *
     * @return string
     */
    protected function _getQuery() {
        // looking for table/view
        $sCountryTable = getViewName('oxcountry');
        $sCountryId      = oxConfig::getParameter( 'oxid' );
        $sSynchCountryId = oxConfig::getParameter( 'synchoxid' );        
        $sType           = oxConfig::getParameter( 'type' );

        // category selected or not ?
        if ( !$sCountryId) {
            // which fields to load ?
            $sQAdd = " from $sCountryTable where $sCountryTable.oxactive = '1' ";
        } else {

            $sQAdd  = " from fcpopayment2country left join $sCountryTable on $sCountryTable.oxid=fcpopayment2country.fcpo_countryid ";
            $sQAdd .= "where $sCountryTable.oxactive = '1' and fcpopayment2country.fcpo_paymentid = '$sCountryId' and fcpopayment2country.fcpo_type = '{$sType}' ";
        }

        if ( $sSynchCountryId && $sSynchCountryId != $sCountryId ) {
            $sQAdd .= "and $sCountryTable.oxid not in ( ";
            $sQAdd .= "select $sCountryTable.oxid from fcpopayment2country left join $sCountryTable on $sCountryTable.oxid=fcpopayment2country.fcpo_countryid ";
            $sQAdd .= "where fcpopayment2country.fcpo_paymentid = '$sSynchCountryId' and fcpopayment2country.fcpo_type = '{$sType}' ) ";
        }

        return $sQAdd;
    }

    /**
     * Adds chosen country to payment
     *
     * @return null
     */
    public function addpaycountry() {
        $aChosenCntr = $this->_getActionIds( 'oxcountry.oxid' );
        $soxId       = oxConfig::getParameter( 'synchoxid');
        $sType       = oxConfig::getParameter( 'type' );
        if ( oxConfig::getParameter( 'all' ) ) {
            $sCountryTable = getViewName('oxcountry');
            $aChosenCntr = $this->_getAll( $this->_addFilter( "select $sCountryTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenCntr ) ) {
            foreach ( $aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew( 'oxbase' );
                $oObject2Payment->init( 'fcpopayment2country' );
                $oObject2Payment->fcpopayment2country__fcpo_paymentid  = new oxField($soxId);
                $oObject2Payment->fcpopayment2country__fcpo_countryid  = new oxField($sChosenCntr);
                $oObject2Payment->fcpopayment2country__fcpo_type       = new oxField($sType);
                $oObject2Payment->save();
            }
        }
    }

    /**
     * Removes chosen country from payment
     *
     * @return null
     */
    public function removepaycountry() {
        $aChosenCntr = $this->_getActionIds( 'fcpopayment2country.oxid' );
        if ( oxConfig::getParameter( 'all' ) ) {
            $sQ = $this->_addFilter( "delete fcpopayment2country.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );
        } elseif ( is_array( $aChosenCntr ) ) {
            $sQ = "delete from fcpopayment2country where fcpopayment2country.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenCntr ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }
    
}
