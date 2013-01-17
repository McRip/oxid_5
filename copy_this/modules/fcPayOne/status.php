<?php

set_time_limit(0);
ini_set ('memory_limit', '1024M');
ini_set ('log_errors', 1);
ini_set ('error_log', 'error.log');


if (!function_exists('getShopBasePath')) {
    /**
     * Returns shop base path.
     *
     * @return string
     */
    function getShopBasePath()
    {
        return dirname(__FILE__).'/../../';
    }
}

set_include_path(get_include_path() . PATH_SEPARATOR . getShopBasePath());

/**
 * Returns true.
 *
 * @return bool
 */
if ( !function_exists( 'isAdmin' )) {
    function isAdmin()
    {
        return true;
    }
}

error_reporting( E_ALL ^ E_NOTICE );

// custom functions file
require getShopBasePath() . 'modules/functions.php';

// Generic utility method file
require_once getShopBasePath() . 'core/oxfunctions.php';

//strips magics quote if any
oxUtils::getInstance()->stripGpcMagicQuotes();

function get($key) {
    if(array_key_exists($key, $_POST)) {
        $sString = $_POST[$key];
        if(oxConfig::getInstance()->isUtf()) {
            $sString = utf8_encode($sString);
        }
        return mysql_real_escape_string($sString);
    }
    return '';
}

class fcPayOneTransactionStatusHandler {

    public function log() {
        $sQuery = "SELECT oxordernr FROM oxorder WHERE fcpotxid = '".get('txid')."' LIMIT 1";
        $oResult = oxDb::getDb()->Execute($sQuery);
        $iOrderNr = '0';
        
        if ( $oResult != false && $oResult->recordCount() > 0 ) {
            while (!$oResult->EOF) {
                $iOrderNr = $oResult->fields[0];
                $oResult->moveNext();
            }
        }

        $sQuery = "
            INSERT INTO fcpotransactionstatus (
                FCPO_ORDERNR,   FCPO_KEY,           FCPO_TXACTION,          FCPO_PORTALID,          FCPO_AID,           FCPO_CLEARINGTYPE,          FCPO_TXTIME,                        FCPO_CURRENCY,          FCPO_USERID,            FCPO_ACCESSNAME,            FCPO_ACCESSCODE,            FCPO_PARAM,         FCPO_MODE,          FCPO_PRICE,         FCPO_TXID,          FCPO_REFERENCE,         FCPO_SEQUENCENUMBER,            FCPO_COMPANY,           FCPO_FIRSTNAME,         FCPO_LASTNAME,          FCPO_STREET,            FCPO_ZIP,           FCPO_CITY,          FCPO_EMAIL,         FCPO_COUNTRY,           FCPO_SHIPPING_COMPANY,          FCPO_SHIPPING_FIRSTNAME,            FCPO_SHIPPING_LASTNAME,         FCPO_SHIPPING_STREET,           FCPO_SHIPPING_ZIP,          FCPO_SHIPPING_CITY,         FCPO_SHIPPING_COUNTRY,          FCPO_BANKCOUNTRY,           FCPO_BANKACCOUNT,           FCPO_BANKCODE,          FCPO_BANKACCOUNTHOLDER,         FCPO_CARDEXPIREDATE,            FCPO_CARDTYPE,          FCPO_CARDPAN,           FCPO_CUSTOMERID,            FCPO_BALANCE,           FCPO_RECEIVABLE,        FCPO_CLEARING_BANKACCOUNTHOLDER,        FCPO_CLEARING_BANKACCOUNT,          FCPO_CLEARING_BANKCODE,         FCPO_CLEARING_BANKNAME,         FCPO_CLEARING_BANKBIC,          FCPO_CLEARING_BANKIBAN,         FCPO_CLEARING_LEGALNOTE,        FCPO_CLEARING_DUEDATE,          FCPO_CLEARING_REFERENCE,        FCPO_CLEARING_INSTRUCTIONNOTE
            ) VALUES (
                '{$iOrderNr}',  '".get('key')."',   '".get('txaction')."',  '".get('portalid')."',  '".get('aid')."',   '".get('clearingtype')."',  FROM_UNIXTIME('".get('txtime')."'), '".get('currency')."',  '".get('userid')."',    '".get('accessname')."',    '".get('accesscode')."',    '".get('param')."', '".get('mode')."',  '".get('price')."', '".get('txid')."',  '".get('reference')."', '".get('sequencenumber')."',    '".get('company')."',   '".get('firstname')."', '".get('lastname')."',  '".get('street')."',    '".get('zip')."',   '".get('city')."',  '".get('email')."', '".get('country')."',   '".get('shipping_company')."',  '".get('shipping_firstname')."',    '".get('shipping_lastname')."', '".get('shipping_street')."',   '".get('shipping_zip')."',  '".get('shipping_city')."', '".get('shipping_country')."',  '".get('bankcountry')."',   '".get('bankaccount')."',   '".get('bankcode')."',  '".get('bankaccountholder')."', '".get('cardexpiredate')."',    '".get('cardtype')."',  '".get('cardpan')."',   '".get('customerid')."',    '".get('balance')."',   '".get('receivable')."','".get('clearing_bankaccountholder')."','".get('clearing_bankaccount')."',  '".get('clearing_bankcode')."', '".get('clearing_bankname')."', '".get('clearing_bankbic')."',  '".get('clearing_bankiban')."', '".get('clearing_legalnote')."','".get('clearing_duedate')."',  '".get('clearing_reference')."','".get('clearing_instructionnote')."'
            )";
        oxDb::getDb()->Execute($sQuery);
        $error = mysql_error();
        if($error != '') {
            error_log($error."\n".$sQuery);
        }
    }

    public function handle() {
        $this->log();
        $sOrderId = oxDb::getDb()->GetOne("SELECT oxid FROM oxorder WHERE fcpotxid = '".get('txid')."'");
        if($sOrderId) {
            $oOrder = oxNew('oxorder');
            $oOrder->load($sOrderId);
            if($oOrder->allowDebit()) {
                $query = "UPDATE oxorder SET oxpaid = NOW() WHERE oxid = '{$sOrderId}'";
                oxDb::getDb()->Execute($query);
            }
            if(get('txaction') == 'paid') {
                $query = "UPDATE oxorder SET oxfolder = 'ORDERFOLDER_NEW', oxtransstatus = 'OK' WHERE oxid = '{$sOrderId}' AND oxtransstatus = 'INCOMPLETE' AND oxfolder = 'ORDERFOLDER_PROBLEMS'";
                oxDb::getDb()->Execute($query);
            }
        }

        echo 'TSOK';
    }

}

ob_start();
echo date('Y-m-d H:i:s').' ';
print_r($_POST);
error_log(ob_get_contents(), 3, dirname(__FILE__).'/transactions.log');
ob_end_clean();

$oLogger = new fcPayOneTransactionStatusHandler();
$oLogger->handle();