<?php
require_once dirname(__FILE__) . "/bootstrap.php";

class fcpoSetup {

    public static function onActivate() {
        // initializes singleton config class
        $myConfig = oxRegistry::getConfig();
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
        $sQueryAlterOxorderNotChecked = "ALTER TABLE oxorder ADD COLUMN FCPOORDERNOTCHECKED TINYINT(1) DEFAULT '0' NOT NULL;";

        //Needed for 12 digit long ids
        $sQueryChangeToVarchar1 = "ALTER TABLE fcpotransactionstatus CHANGE FCPO_USERID FCPO_USERID VARCHAR(32) DEFAULT '0' NOT NULL;";
        $sQueryChangeToVarchar2 = "ALTER TABLE fcpotransactionstatus CHANGE FCPO_TXID FCPO_TXID VARCHAR(32) DEFAULT '0' NOT NULL;";

        $sQueryTableFcpocheckedaddresses = "
            CREATE TABLE fcpocheckedaddresses (
              fcpo_address_hash CHAR(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
              fcpo_checkdate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (fcpo_address_hash)
            ) ENGINE=INNODB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

        //CREATE NEW TABLES
        self::addTableIfNotExists('fcporefnr', $sQueryTableFcporefnr);
        self::addTableIfNotExists('fcporequestlog', $sQueryTableFcporequestlog);
        self::addTableIfNotExists('fcpotransactionstatus', $sQueryTableFcpotransactionstatus);
        self::addTableIfNotExists('fcpopayment2country', $sQueryTableFcpopayment2country);
        self::addTableIfNotExists('fcpocheckedaddresses', $sQueryTableFcpocheckedaddresses);

        //ALTER EXISTING TABLES
        self::addColumnIfNotExists('oxorder', 'FCPOTXID', $sQueryAlterOxorderTxid);
        self::addColumnIfNotExists('oxorder', 'FCPOREFNR', $sQueryAlterOxorderRefNr);
        self::addColumnIfNotExists('oxorder', 'FCPOAUTHMODE', $sQueryAlterOxorderAuthMode);
        self::addColumnIfNotExists('oxorder', 'FCPOMODE', $sQueryAlterOxorderMode);

        self::addColumnIfNotExists('oxorder', 'FCPODELCOSTDEBITED', $sQueryAlterOxorderDelcostDebited);
        self::addColumnIfNotExists('oxorder', 'FCPOPAYCOSTDEBITED', $sQueryAlterOxorderPaycostDebited);
        self::addColumnIfNotExists('oxorder', 'FCPOWRAPCOSTDEBITED', $sQueryAlterOxorderWrapcostDebited);
        self::addColumnIfNotExists('oxorder', 'FCPOVOUCHERDISCOUNTDEBITED', $sQueryAlterOxorderVoucherdiscountDebited);
        self::addColumnIfNotExists('oxorder', 'FCPODISCOUNTDEBITED', $sQueryAlterOxorderDiscountDebited);
        self::addColumnIfNotExists('oxorder', 'FCPOORDERNOTCHECKED', $sQueryAlterOxorderNotChecked);

        self::addColumnIfNotExists('oxorderarticles', 'FCPOCAPTUREDAMOUNT', $sQueryAlterOxorderarticlesCapturedAmount);
        self::addColumnIfNotExists('oxorderarticles', 'FCPODEBITEDAMOUNT', $sQueryAlterOxorderarticlesDebitedAmount);

        self::addColumnIfNotExists('oxpayments', 'FCPOISPAYONE', $sQueryAlterOxpaymentsIsPayone);
        self::addColumnIfNotExists('oxpayments', 'FCPOAUTHMODE', $sQueryAlterOxpaymentsAuthMode);
        self::addColumnIfNotExists('oxpayments', 'FCPOLIVEMODE', $sQueryAlterOxpaymentsLiveMode);

        self::addColumnIfNotExists('oxuser', 'FCPOBONICHECKDATE', $sQueryAlterOxuser);

        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKACCOUNTHOLDER', $sQueryAlterTxStatusClearing1);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKACCOUNT', $sQueryAlterTxStatusClearing2);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKCODE', $sQueryAlterTxStatusClearing3);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKNAME', $sQueryAlterTxStatusClearing4);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKBIC', $sQueryAlterTxStatusClearing5);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_BANKIBAN', $sQueryAlterTxStatusClearing6);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_LEGALNOTE', $sQueryAlterTxStatusClearing7);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_DUEDATE', $sQueryAlterTxStatusClearing8);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_REFERENCE', $sQueryAlterTxStatusClearing9);
        self::addColumnIfNotExists('fcpotransactionstatus', 'FCPO_CLEARING_INSTRUCTIONNOTE', $sQueryAlterTxStatusClearing10);

        self::changeColumnTypeIfWrong('fcpotransactionstatus', 'FCPO_USERID', 'varchar(32)', $sQueryChangeToVarchar1);
        self::changeColumnTypeIfWrong('fcpotransactionstatus', 'FCPO_TXID',   'varchar(32)', $sQueryChangeToVarchar2);

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
                self::insertRowIfNotExists('oxpayments', array('OXID' => $sPaymentOxid), "INSERT INTO oxpayments(OXID,OXACTIVE,OXDESC,OXADDSUM,OXADDSUMTYPE,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC,OXCHECKED,OXDESC_1,OXVALDESC_1,OXDESC_2,OXVALDESC_2,OXDESC_3,OXVALDESC_3,OXLONGDESC,OXLONGDESC_1,OXLONGDESC_2,OXLONGDESC_3,OXSORT,FCPOISPAYONE,FCPOAUTHMODE,FCPOLIVEMODE) VALUES ('{$sPaymentOxid}', 1, '{$sPaymentName}', 0, 'abs', 0, 100, 5000, '', 0, '{$sPaymentName}', '', '', '', '', '', '', '', '', '', 0, 1, 'preauthorization', 0);");
            } else {
                self::insertRowIfNotExists('oxpayments', array('OXID' => $sPaymentOxid), "INSERT INTO oxpayments(OXID,OXACTIVE,OXDESC,OXADDSUM,OXADDSUMTYPE,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC,OXCHECKED,OXDESC_1,OXVALDESC_1,OXDESC_2,OXVALDESC_2,OXDESC_3,OXVALDESC_3,OXLONGDESC,OXLONGDESC_1,OXLONGDESC_2,OXLONGDESC_3,OXSORT,FCPOISPAYONE,FCPOAUTHMODE,FCPOLIVEMODE) VALUES ('{$sPaymentOxid}', 1, '{$sPaymentName}', 0, 'abs', 0, 0, 1000000, '', 0, '{$sPaymentName}', '', '', '', '', '', '', '', '', '', 0, 1, 'preauthorization', 0);");
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

            self::insertRowIfNotExists('oxobject2payment', array('OXPAYMENTID' => $sPaymentOxid, 'OXTYPE' => 'oxdelset'), "INSERT INTO oxobject2payment(OXID,OXPAYMENTID,OXOBJECTID,OXTYPE) values (MD5(CONCAT(NOW(),RAND())), '{$sPaymentOxid}', 'oxidstandard', 'oxdelset');");
        }
    }

    public static function onDeactivate() {
        return true;
    }

    private static function addTableIfNotExists($sTableName, $sQuery) {
        if(oxDb::getDb()->Execute("SHOW TABLES LIKE '{$sTableName}'")->EOF) {
            oxDb::getDb()->Execute($sQuery);
            echo 'Tabelle '.$sTableName.' hinzugef&uuml;gt.<br>';
            return true;
        }
        return false;
    }

    private static function addColumnIfNotExists($sTableName, $sColumnName, $sQuery) {
        if(oxDb::getDb()->Execute("SHOW COLUMNS FROM {$sTableName} LIKE '{$sColumnName}'")->EOF) {
            oxDb::getDb()->Execute($sQuery);
            echo 'In Tabelle '.$sTableName.' Spalte '.$sColumnName.' hinzugef&uuml;gt.<br>';
            return true;
        }
        return false;
    }

    private static function insertRowIfNotExists($sTableName, $aKeyValue, $sQuery) {
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

    private static function changeColumnTypeIfWrong($sTableName, $sColumnName, $sExpectedType, $sQuery) {
        if(oxDb::getDb()->Execute("SHOW COLUMNS FROM {$sTableName} WHERE FIELD = '{$sColumnName}' AND TYPE = '{$sExpectedType}'")->EOF) {
            oxDb::getDb()->Execute($sQuery);
            echo 'In Tabelle '.$sTableName.' Spalte '.$sColumnName.' auf Typ '.$sExpectedType.' umgestellt.<br>';
            return true;
        }
        return false;
    }
}