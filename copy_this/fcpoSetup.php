<?php

if(file_exists(dirname(__FILE__)."/bootstrap.php")) {
    require_once dirname(__FILE__) . "/bootstrap.php";
} else {
    //setting basic configuration parameters
    ini_set('session.name', 'sid' );
    ini_set('session.use_cookies', 0 );
    ini_set('session.use_trans_sid', 0);
    ini_set('url_rewriter.tags', '');
    ini_set('magic_quotes_runtime', 0);

    /**
     * Returns shop base path.
     *
     * @return string
     */
    function getShopBasePath()
    {
        return dirname(__FILE__).'/';
    }

    if ( !function_exists( 'isAdmin' )) {
        /**
         * Returns false.
         *
         * @return bool
         */
        function isAdmin()
        {
            return false;
        }
    }

    // custom functions file
    require getShopBasePath() . 'modules/functions.php';

    // Generic utility method file
    require_once getShopBasePath() . 'core/oxfunctions.php';
}

function addTableIfNotExists($sTableName, $sQuery) {
    if(oxDb::getDb()->Execute("SHOW TABLES LIKE '{$sTableName}'")->EOF) {
        oxDb::getDb()->Execute($sQuery);
        echo 'Tabelle '.$sTableName.' hinzugef&uuml;gt.<br>';
        return true;
    }
    return false;
}

function addColumnIfNotExists($sTableName, $sColumnName, $sQuery) {
    if(oxDb::getDb()->Execute("SHOW COLUMNS FROM {$sTableName} LIKE '{$sColumnName}'")->EOF) {
        oxDb::getDb()->Execute($sQuery);
        echo 'In Tabelle '.$sTableName.' Spalte '.$sColumnName.' hinzugef&uuml;gt.<br>';
        return true;
    }
    return false;
}

function insertRowIfNotExists($sTableName, $aKeyValue, $sQuery) {
    $sWhere = '';
    foreach ($aKeyValue as $key => $value) {
        $sWhere .= " AND $key = '$value'";
    }
    if(oxDb::getDb()->Execute("SELECT * FROM {$sTableName} WHERE 1".$sWhere)->EOF) {
        oxDb::getDb()->Execute($sQuery);
        echo 'In Tabelle '.$sTableName.' neuen Eintrag erstellt.<br>';
        return true;
    }
    return false;
}

function changeColumnTypeIfWrong($sTableName, $sColumnName, $sExpectedType, $sQuery) {
    if(oxDb::getDb()->Execute("SHOW COLUMNS FROM {$sTableName} WHERE FIELD = '{$sColumnName}' AND TYPE = '{$sExpectedType}'")->EOF) {
        oxDb::getDb()->Execute($sQuery);
        echo 'In Tabelle '.$sTableName.' Spalte '.$sColumnName.' auf Typ '.$sExpectedType.' umgestellt.<br>';
        return true;
    }
    return false;
}

function getCurrentVersion() {
    return versionToInt(oxConfig::getInstance()->getActiveShop()->oxshops__oxversion->value);
}

function versionToInt($sVersion) {
    $iVersion = (int)str_replace('.', '', $sVersion);
    while ($iVersion < 1000) {
        $iVersion = $iVersion*10;
    }
    return $iVersion;
}

function isUnderVersion($sMaxVersion) {
    $iMaxVersion = versionToInt($sMaxVersion);
    $iCurrVersion = getCurrentVersion();
    if($iCurrVersion < $iMaxVersion) {
        return true;
    }
    return false;
}

function isOverVersion($sMinVersion, $blEqualOrGreater = false) {
    $iMinVersion = versionToInt($sMinVersion);
    $iCurrVersion = getCurrentVersion();
    if($blEqualOrGreater === false) {
        if($iCurrVersion > $iMinVersion) {
            return true;
        }
    } else {
        if($iCurrVersion >= $iMinVersion) {
            return true;
        }        
    }
    return false;
}

function isBetweenVersions($sMinVersion, $sMaxVersion) {
    if(!isOverVersion($sMinVersion, true)) {
        return false;
    }
    if(!isUnderVersion($sMaxVersion)) {
        return false;
    }
    return true;
}

function copyFile($sSource, $sDestination) {
    if(file_exists($sSource) === true) {
        if(file_exists($sDestination)) {
            if(md5_file($sSource) != md5_file($sDestination)) {
                unlink($sDestination);
            } else {
                return;
            }
        }
        copy($sSource, $sDestination);
        echo 'Datei '.$sDestination.' in Theme kopiert.<br>';
    }
}

// initializes singleton config class
$myConfig = oxConfig::getInstance();
$oDb = oxDb::getDb();

$sShopEdition = $myConfig->getActiveShop()->oxshops__oxedition->value;
$sShopId = 'oxbaseshop';
if($sShopEdition == 'EE') {
    $sShopId = '1';
}

if($myConfig->isUtf()) {
    $sQueryTableFcporefnr = "
        CREATE TABLE fcporefnr (
          FCPO_REFNR int(11) NOT NULL AUTO_INCREMENT,
          FCPO_TXID varchar(32) NOT NULL DEFAULT '',
          PRIMARY KEY (FCPO_REFNR)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    $sQueryTableFcporequestlog = "
        CREATE TABLE fcporequestlog (
          OXID int(11) NOT NULL AUTO_INCREMENT,
          FCPO_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FCPO_REFNR int(11) NOT NULL DEFAULT '0',
          FCPO_REQUESTTYPE varchar(32) NOT NULL DEFAULT '',
          FCPO_RESPONSESTATUS varchar(32) NOT NULL DEFAULT '',
          FCPO_REQUEST text NOT NULL,
          FCPO_RESPONSE text NOT NULL,
          FCPO_PORTALID varchar(32) NOT NULL DEFAULT '',
          FCPO_AID varchar(32) NOT NULL DEFAULT '',
          PRIMARY KEY (OXID)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    $sQueryTableFcpotransactionstatus = "
        CREATE TABLE fcpotransactionstatus (
          OXID int(11) NOT NULL AUTO_INCREMENT,
          FCPO_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FCPO_ORDERNR int(11) DEFAULT '0',
          FCPO_KEY varchar(32) NOT NULL DEFAULT '',
          FCPO_TXACTION varchar(32) NOT NULL DEFAULT '',
          FCPO_PORTALID int(11) NOT NULL DEFAULT '0',
          FCPO_AID int(11) NOT NULL DEFAULT '0',
          FCPO_CLEARINGTYPE varchar(32) NOT NULL DEFAULT '',
          FCPO_TXTIME timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          FCPO_CURRENCY varchar(32) NOT NULL DEFAULT '',
          FCPO_USERID int(11) NOT NULL DEFAULT '0',
          FCPO_ACCESSNAME varchar(32) NOT NULL DEFAULT '',
          FCPO_ACCESSCODE varchar(32) NOT NULL DEFAULT '',
          FCPO_PARAM varchar(255) NOT NULL DEFAULT '',
          FCPO_MODE varchar(8) NOT NULL DEFAULT '',
          FCPO_PRICE double NOT NULL DEFAULT '0',
          FCPO_TXID int(11) NOT NULL DEFAULT '0',
          FCPO_REFERENCE int(11) NOT NULL DEFAULT '0',
          FCPO_SEQUENCENUMBER int(11) NOT NULL DEFAULT '0',
          FCPO_COMPANY varchar(255) NOT NULL DEFAULT '',
          FCPO_FIRSTNAME varchar(255) NOT NULL DEFAULT '',
          FCPO_LASTNAME varchar(255) NOT NULL DEFAULT '',
          FCPO_STREET varchar(255) NOT NULL DEFAULT '',
          FCPO_ZIP varchar(16) NOT NULL DEFAULT '',
          FCPO_CITY varchar(255) NOT NULL DEFAULT '',
          FCPO_EMAIL varchar(255) NOT NULL DEFAULT '',
          FCPO_COUNTRY varchar(8) NOT NULL DEFAULT '',
          FCPO_SHIPPING_COMPANY varchar(255) NOT NULL DEFAULT '',
          FCPO_SHIPPING_FIRSTNAME varchar(255) NOT NULL DEFAULT '',
          FCPO_SHIPPING_LASTNAME varchar(255) NOT NULL DEFAULT '',
          FCPO_SHIPPING_STREET varchar(255) NOT NULL DEFAULT '',
          FCPO_SHIPPING_ZIP varchar(16) NOT NULL DEFAULT '',
          FCPO_SHIPPING_CITY varchar(255) NOT NULL DEFAULT '',
          FCPO_SHIPPING_COUNTRY varchar(8) NOT NULL DEFAULT '',
          FCPO_BANKCOUNTRY varchar(8) NOT NULL DEFAULT '',
          FCPO_BANKACCOUNT varchar(32) NOT NULL DEFAULT '',
          FCPO_BANKCODE varchar(32) NOT NULL DEFAULT '',
          FCPO_BANKACCOUNTHOLDER varchar(255) NOT NULL DEFAULT '',
          FCPO_CARDEXPIREDATE varchar(8) NOT NULL DEFAULT '',
          FCPO_CARDTYPE varchar(8) NOT NULL DEFAULT '',
          FCPO_CARDPAN varchar(32) NOT NULL DEFAULT '',
          FCPO_CUSTOMERID int(11) NOT NULL DEFAULT '0',
          FCPO_BALANCE double NOT NULL DEFAULT '0',
          FCPO_RECEIVABLE double NOT NULL DEFAULT '0',
          PRIMARY KEY (OXID)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    $sQueryTableFcpopayment2country = "
        CREATE TABLE IF NOT EXISTS fcpopayment2country (
          OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
          FCPO_PAYMENTID char(8) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_COUNTRYID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_TYPE char(8) NOT NULL DEFAULT '',
          PRIMARY KEY (`OXID`),
          KEY `FCPO_PAYMENTID` (`FCPO_PAYMENTID`),
          KEY `FCPO_COUNTRYID` (`FCPO_COUNTRYID`),
          KEY `FCPO_TYPE` (`FCPO_TYPE`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    $sQueryAlterOxorderTxid     = "ALTER TABLE oxorder ADD COLUMN FCPOTXID VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterOxorderRefNr    = "ALTER TABLE oxorder ADD COLUMN FCPOREFNR INT(11) DEFAULT '0' NOT NULL;";
    $sQueryAlterOxorderAuthMode = "ALTER TABLE oxorder ADD COLUMN FCPOAUTHMODE VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterOxorderMode     = "ALTER TABLE oxorder ADD COLUMN FCPOMODE VARCHAR(8) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";

    $sQueryAlterOxpaymentsAuthMode = "ALTER TABLE oxpayments ADD COLUMN FCPOAUTHMODE VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    
    $sQueryAlterTxStatusClearing1  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKACCOUNTHOLDER VARCHAR(64) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing2  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKACCOUNT VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing3  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKCODE VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing4  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKNAME VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing5  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKBIC VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing6  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKIBAN VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing7  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_LEGALNOTE VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing8  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_DUEDATE VARCHAR(32) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing9  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_REFERENCE VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing10 = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_INSTRUCTIONNOTE VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;";
} else {
    $sQueryTableFcporefnr = "
        CREATE TABLE fcporefnr (
          FCPO_REFNR int(11) NOT NULL AUTO_INCREMENT,
          FCPO_TXID varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          PRIMARY KEY (FCPO_REFNR)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

    $sQueryTableFcporequestlog = "
        CREATE TABLE fcporequestlog (
          OXID int(11) NOT NULL AUTO_INCREMENT,
          FCPO_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FCPO_REFNR int(11) NOT NULL DEFAULT '0',
          FCPO_REQUESTTYPE varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_RESPONSESTATUS varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_REQUEST text COLLATE latin1_general_ci NOT NULL,
          FCPO_RESPONSE text COLLATE latin1_general_ci NOT NULL,
          FCPO_PORTALID varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_AID varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          PRIMARY KEY (OXID)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

    $sQueryTableFcpotransactionstatus = "
        CREATE TABLE fcpotransactionstatus (
          OXID int(11) NOT NULL AUTO_INCREMENT,
          FCPO_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FCPO_ORDERNR int(11) DEFAULT '0',
          FCPO_KEY varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_TXACTION varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_PORTALID int(11) NOT NULL DEFAULT '0',
          FCPO_AID int(11) NOT NULL DEFAULT '0',
          FCPO_CLEARINGTYPE varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_TXTIME timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          FCPO_CURRENCY varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_USERID int(11) NOT NULL DEFAULT '0',
          FCPO_ACCESSNAME varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_ACCESSCODE varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_PARAM varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_MODE varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_PRICE double NOT NULL DEFAULT '0',
          FCPO_TXID int(11) NOT NULL DEFAULT '0',
          FCPO_REFERENCE int(11) NOT NULL DEFAULT '0',
          FCPO_SEQUENCENUMBER int(11) NOT NULL DEFAULT '0',
          FCPO_COMPANY varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_FIRSTNAME varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_LASTNAME varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_STREET varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_ZIP varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_CITY varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_EMAIL varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_COUNTRY varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_COMPANY varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_FIRSTNAME varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_LASTNAME varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_STREET varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_ZIP varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_CITY varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_SHIPPING_COUNTRY varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_BANKCOUNTRY varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_BANKACCOUNT varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_BANKCODE varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_BANKACCOUNTHOLDER varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_CARDEXPIREDATE varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_CARDTYPE varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_CARDPAN varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_CUSTOMERID int(11) NOT NULL DEFAULT '0',
          FCPO_BALANCE double NOT NULL DEFAULT '0',
          FCPO_RECEIVABLE double NOT NULL DEFAULT '0',
          PRIMARY KEY (OXID)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

    $sQueryTableFcpopayment2country = "
        CREATE TABLE IF NOT EXISTS fcpopayment2country (
          OXID char(32) COLLATE latin1_general_ci NOT NULL,
          FCPO_PAYMENTID char(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_COUNTRYID char(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          FCPO_TYPE char(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
          PRIMARY KEY (`OXID`),
          KEY `FCPO_PAYMENTID` (`FCPO_PAYMENTID`),
          KEY `FCPO_COUNTRYID` (`FCPO_COUNTRYID`),
          KEY `FCPO_TYPE` (`FCPO_TYPE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

    $sQueryAlterOxorderTxid     = "ALTER TABLE oxorder ADD COLUMN FCPOTXID VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterOxorderRefNr    = "ALTER TABLE oxorder ADD COLUMN FCPOREFNR INT(11) DEFAULT '0' NOT NULL";
    $sQueryAlterOxorderAuthMode = "ALTER TABLE oxorder ADD COLUMN FCPOAUTHMODE VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL";
    $sQueryAlterOxorderMode     = "ALTER TABLE oxorder ADD COLUMN FCPOMODE VARCHAR(8) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";

    $sQueryAlterOxpaymentsAuthMode = "ALTER TABLE oxpayments ADD COLUMN FCPOAUTHMODE VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    
    $sQueryAlterTxStatusClearing1  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKACCOUNTHOLDER VARCHAR(64) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing2  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKACCOUNT VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing3  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKCODE VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing4  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKNAME VARCHAR(255) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing5  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKBIC VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing6  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_BANKIBAN VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing7  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_LEGALNOTE VARCHAR(255) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing8  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_DUEDATE VARCHAR(32) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing9  = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_REFERENCE VARCHAR(255) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
    $sQueryAlterTxStatusClearing10 = "ALTER TABLE fcpotransactionstatus ADD COLUMN FCPO_CLEARING_INSTRUCTIONNOTE VARCHAR(255) CHARSET latin1 COLLATE latin1_general_ci DEFAULT '' NOT NULL;";
}
$sQueryAlterOxuser = "ALTER TABLE oxuser ADD COLUMN FCPOBONICHECKDATE DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL;";

$sQueryAlterOxpaymentsLiveMode = "ALTER TABLE oxpayments ADD COLUMN FCPOLIVEMODE TINYINT(1) DEFAULT '0' NOT NULL;";
$sQueryAlterOxpaymentsIsPayone = "ALTER TABLE oxpayments ADD COLUMN FCPOISPAYONE TINYINT(1) DEFAULT '0' NOT NULL;";

$sQueryAlterOxorderarticlesCapturedAmount = "ALTER TABLE oxorderarticles ADD COLUMN FCPOCAPTUREDAMOUNT INT(11) DEFAULT '0' NOT NULL;";
$sQueryAlterOxorderarticlesDebitedAmount = "ALTER TABLE oxorderarticles ADD COLUMN FCPODEBITEDAMOUNT INT(11) DEFAULT '0' NOT NULL;";

$sQueryAlterOxorderDelcostDebited = "ALTER TABLE oxorder ADD COLUMN FCPODELCOSTDEBITED TINYINT(1) DEFAULT '0' NOT NULL;";
$sQueryAlterOxorderPaycostDebited = "ALTER TABLE oxorder ADD COLUMN FCPOPAYCOSTDEBITED TINYINT(1) DEFAULT '0' NOT NULL;";
$sQueryAlterOxorderWrapcostDebited = "ALTER TABLE oxorder ADD COLUMN FCPOWRAPCOSTDEBITED TINYINT(1) DEFAULT '0' NOT NULL;";
$sQueryAlterOxorderVoucherdiscountDebited = "ALTER TABLE oxorder ADD COLUMN FCPOVOUCHERDISCOUNTDEBITED TINYINT(1) DEFAULT '0' NOT NULL;";
$sQueryAlterOxorderDiscountDebited = "ALTER TABLE oxorder ADD COLUMN FCPODISCOUNTDEBITED TINYINT(1) DEFAULT '0' NOT NULL;";

//Needed for 12 digit long ids
$sQueryChangeToVarchar1 = "ALTER TABLE fcpotransactionstatus CHANGE FCPO_USERID FCPO_USERID VARCHAR(32) DEFAULT '0' NOT NULL;";
$sQueryChangeToVarchar2 = "ALTER TABLE fcpotransactionstatus CHANGE FCPO_TXID FCPO_TXID VARCHAR(32) DEFAULT '0' NOT NULL;";

//CREATE NEW TABLES
addTableIfNotExists('fcporefnr', $sQueryTableFcporefnr);
addTableIfNotExists('fcporequestlog', $sQueryTableFcporequestlog);
addTableIfNotExists('fcpotransactionstatus', $sQueryTableFcpotransactionstatus);
addTableIfNotExists('fcpopayment2country', $sQueryTableFcpopayment2country);

//ALTER EXISTING TABLES
addColumnIfNotExists('oxorder', 'FCPOTXID', $sQueryAlterOxorderTxid);
addColumnIfNotExists('oxorder', 'FCPOREFNR', $sQueryAlterOxorderRefNr);
addColumnIfNotExists('oxorder', 'FCPOAUTHMODE', $sQueryAlterOxorderAuthMode);
addColumnIfNotExists('oxorder', 'FCPOMODE', $sQueryAlterOxorderMode);

addColumnIfNotExists('oxorder', 'FCPODELCOSTDEBITED', $sQueryAlterOxorderDelcostDebited);
addColumnIfNotExists('oxorder', 'FCPOPAYCOSTDEBITED', $sQueryAlterOxorderPaycostDebited);
addColumnIfNotExists('oxorder', 'FCPOWRAPCOSTDEBITED', $sQueryAlterOxorderWrapcostDebited);
addColumnIfNotExists('oxorder', 'FCPOVOUCHERDISCOUNTDEBITED', $sQueryAlterOxorderVoucherdiscountDebited);
addColumnIfNotExists('oxorder', 'FCPODISCOUNTDEBITED', $sQueryAlterOxorderDiscountDebited);

addColumnIfNotExists('oxorderarticles', 'FCPOCAPTUREDAMOUNT', $sQueryAlterOxorderarticlesCapturedAmount);
addColumnIfNotExists('oxorderarticles', 'FCPODEBITEDAMOUNT', $sQueryAlterOxorderarticlesDebitedAmount);

addColumnIfNotExists('oxpayments', 'FCPOISPAYONE', $sQueryAlterOxpaymentsIsPayone);
addColumnIfNotExists('oxpayments', 'FCPOAUTHMODE', $sQueryAlterOxpaymentsAuthMode);
addColumnIfNotExists('oxpayments', 'FCPOLIVEMODE', $sQueryAlterOxpaymentsLiveMode);

addColumnIfNotExists('oxuser', 'FCPOBONICHECKDATE', $sQueryAlterOxuser);

addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKACCOUNTHOLDER', $sQueryAlterTxStatusClearing1);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKACCOUNT', $sQueryAlterTxStatusClearing2);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKCODE', $sQueryAlterTxStatusClearing3);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKNAME', $sQueryAlterTxStatusClearing4);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKBIC', $sQueryAlterTxStatusClearing5);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKIBAN', $sQueryAlterTxStatusClearing6);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_LEGALNOTE', $sQueryAlterTxStatusClearing7);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_DUEDATE', $sQueryAlterTxStatusClearing8);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_REFERENCE', $sQueryAlterTxStatusClearing9);
addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_INSTRUCTIONNOTE', $sQueryAlterTxStatusClearing10);

changeColumnTypeIfWrong('fcpotransactionstatus', 'FCPO_USERID', 'varchar(32)', $sQueryChangeToVarchar1);
changeColumnTypeIfWrong('fcpotransactionstatus', 'FCPO_TXID',   'varchar(32)', $sQueryChangeToVarchar2);

//INSERT PAYMENT METHOD CONFIGURATION
$aPaymentMethods = array(
    'fcpoinvoice' => 'Rechnung',
    'fcpopayadvance' => 'Vorauskasse',
    'fcpodebitnote' => 'Bankeinzug/Lastschrift',
    'fcpocashondel' => 'Nachnahme',
    'fcpocreditcard' => 'Kreditkarte',
    'fcpoonlineueberweisung' => 'Online-Ueberweisung',
    'fcpopaypal' => 'PayPal',
	'fcpocommerzfinanz' => 'Commerz Finanz',
	'fcpobillsafe' => 'BillSAFE',
);
foreach ($aPaymentMethods as $sPaymentOxid => $sPaymentName) {
    //INSERT PAYMENT METHOD
	if($sPaymentOxid == 'fcpocommerzfinanz') {
		insertRowIfNotExists('oxpayments', array('OXID' => $sPaymentOxid), "INSERT INTO oxpayments(OXID,OXACTIVE,OXDESC,OXADDSUM,OXADDSUMTYPE,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC,OXCHECKED,OXDESC_1,OXVALDESC_1,OXDESC_2,OXVALDESC_2,OXDESC_3,OXVALDESC_3,OXLONGDESC,OXLONGDESC_1,OXLONGDESC_2,OXLONGDESC_3,OXSORT,FCPOISPAYONE,FCPOAUTHMODE,FCPOLIVEMODE) VALUES ('{$sPaymentOxid}', 1, '{$sPaymentName}', 0, 'abs', 0, 100, 5000, '', 0, '{$sPaymentName}', '', '', '', '', '', '', '', '', '', 0, 1, 'preauthorization', 0);");
	} else {
        insertRowIfNotExists('oxpayments', array('OXID' => $sPaymentOxid), "INSERT INTO oxpayments(OXID,OXACTIVE,OXDESC,OXADDSUM,OXADDSUMTYPE,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC,OXCHECKED,OXDESC_1,OXVALDESC_1,OXDESC_2,OXVALDESC_2,OXDESC_3,OXVALDESC_3,OXLONGDESC,OXLONGDESC_1,OXLONGDESC_2,OXLONGDESC_3,OXSORT,FCPOISPAYONE,FCPOAUTHMODE,FCPOLIVEMODE) VALUES ('{$sPaymentOxid}', 1, '{$sPaymentName}', 0, 'abs', 0, 0, 1000000, '', 0, '{$sPaymentName}', '', '', '', '', '', '', '', '', '', 0, 1, 'preauthorization', 0);");
    }

    //INSERT PAYMENT METHOD CONFIGURATION
    $blInserted = insertRowIfNotExists('oxobject2group', array('OXSHOPID' => $sShopId, 'OXOBJECTID' => $sPaymentOxid), "INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidadmin');");
    if($blInserted === true) {
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidcustomer');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxiddealer');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidforeigncustomer');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidgoodcust');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidmiddlecust');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidnewcustomer');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidnewsletter');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidnotyetordered');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidpowershopper');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidpricea');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidpriceb');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidpricec');");
        $oDb->Execute("INSERT INTO oxobject2group(OXID,OXSHOPID,OXOBJECTID,OXGROUPSID) values (MD5(CONCAT(NOW(), RAND())), '{$sShopId}', '{$sPaymentOxid}', 'oxidsmallcust');");
    }

    insertRowIfNotExists('oxobject2payment', array('OXPAYMENTID' => $sPaymentOxid, 'OXTYPE' => 'oxdelset'), "INSERT INTO oxobject2payment(OXID,OXPAYMENTID,OXOBJECTID,OXTYPE) values (MD5(CONCAT(NOW(),RAND())), '{$sPaymentOxid}', 'oxidstandard', 'oxdelset');");
}

if(isBetweenVersions('4.5.0', '4.5.99')) {
    insertRowIfNotExists('oxtplblocks', array('OXID' => 'fcpo_payment_override'), "INSERT INTO oxtplblocks(OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) VALUES ('fcpo_payment_override', '1', '{$sShopId}', 'page/checkout/payment.tpl', 'change_payment', '0', 'fcpo_payment_override', 'fcPayOne');");
	insertRowIfNotExists('oxtplblocks', array('OXID' => 'fcpo_payment_select_override'), "INSERT INTO oxtplblocks(OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) VALUES ('fcpo_payment_select_override', '1', '{$sShopId}', 'page/checkout/payment.tpl', 'select_payment', '200', 'fcpo_payment_select_override', 'fcPayOne');");
}

$sPathOut = getShopBasePath().'out/';
$sSetupPath = getShopBasePath().'modules/fcPayOne/setup/';
$oDirHandle = opendir($sPathOut);
$aFolderBlacklist = array(
    '.',
    '..',
    'admin',
    'pictures',
    'media',
    'downloads',
    'de',
    'en',
);
while($sFolder = readdir($oDirHandle)) {
    if(is_dir($sPathOut.$sFolder) && array_search($sFolder, $aFolderBlacklist) === false) {
        if(isUnderVersion('4.7.0')) {
            copyFile($sSetupPath.'theme/de/fcPayOne_lang.php', $sPathOut.$sFolder.'/de/fcPayOne_lang.php');
            copyFile($sSetupPath.'theme/en/fcPayOne_lang.php', $sPathOut.$sFolder.'/en/fcPayOne_lang.php');
        } else {
            copyFile($sSetupPath.'theme/de/fcPayOne_lang.php', getShopBasePath().'application/translations/de/fcPayOne_lang.php');
            copyFile($sSetupPath.'theme/en/fcPayOne_lang.php', getShopBasePath().'application/translations/en/fcPayOne_lang.php');            
        }
        copyFile($sSetupPath.'theme/src/fcPayOne.js', $sPathOut.$sFolder.'/src/fcPayOne.js');
    }
}
closedir($oDirHandle);
if(isUnderVersion('4.5.0')) {
    copyFile($sSetupPath.'admin/tpl/_formparams.tpl', $sPathOut.'admin/tpl/_formparams.tpl');
}

echo 'Fertig. Bitte l&ouml;schen Sie diese Datei.';