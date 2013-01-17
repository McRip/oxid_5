<?php

/**
 * Core class for transaction status sent by PAYONE to the shop
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class fcpoTransactionStatus extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'fcpotransactionstatus';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'fcpotransactionstatus';

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'fcpotransactionstatus' );
    }

    /**
     * Get translated description text of the transaction action
     * 
     * @return string
     */
    public function getAction() {
        $oLang = oxLang::getInstance();
        $sAction = $this->fcpotransactionstatus__fcpo_txaction->value;
        if($sAction == 'paid' && ($this->fcpotransactionstatus__fcpo_txreceivable->value + $this->fcpotransactionstatus__fcpo_balance->value) < 0) {
            $sAction = 'overpaid';
        }
        return $oLang->translateString('fcpo_action_'.$sAction,null,true);
    }

    /**
     * Get translated for payment type of transaction
     * 
     * @return string
     */
    public function getClearingtype() {
        if($this->fcpotransactionstatus__fcpo_clearingtype->value == 'fnc') {
            $sOxid = oxDb::getDb()->GetOne( "SELECT oxid FROM oxorder WHERE fcpotxid = '{$this->fcpotransactionstatus__fcpo_txid->value}'" );
            $oOrder = oxNew('oxorder');
            $oOrder->load($sOxid);
            $oLang = oxLang::getInstance();
            return $oLang->translateString('fcpo_clearingtype_'.$oOrder->oxorder__oxpaymenttype->value,null,true);
        } else {
            $oLang = oxLang::getInstance();
            return $oLang->translateString('fcpo_clearingtype_'.$this->fcpotransactionstatus__fcpo_clearingtype->value,null,true);            
        }
    }

    /**
     * Get total order sum of connected order
     * 
     * @return double
     */
    public function getCaptureAmount() {
        $sOxid = oxDb::getDb()->GetOne( "SELECT oxid FROM oxorder WHERE fcpotxid = '{$this->fcpotransactionstatus__fcpo_txid->value}'" );
        $oOrder = oxNew('oxorder');
        $oOrder->load($sOxid);
        return $oOrder->oxorder__oxtotalordersum;
    }

    /**
     * Get name of creditcard abbreviation
     * 
     * @return string
     */
    public function getCardtype() {
        switch ($this->fcpotransactionstatus__fcpo_cardtype->value) {
            case 'V':
                return 'Visa';
                break;
            case 'M':
                return 'Mastercard';
                break;
            case 'A':
                return 'Amex';
                break;
            case 'D':
                return 'Diners';
                break;
            case 'J':
                return 'JCB';
                break;
            case 'O':
                return 'Maestro International';
                break;
            case 'U':
                return 'Maestro UK';
                break;
            case 'C':
                return 'Discover';
                break;
            case 'B':
                return 'Carte Bleue';
                break;
            default:
                return $this->fcpotransactionstatus__fcpo_cardtype->value;
        }
    }

    
    /**
     * Get translated name of the payment action by currenct receivable money amount
     * 
     * @param double $dReceivable receivable amount
     * 
     * @return string
     */
    public function getDisplayNameReceivable($dReceivable) {
        switch ($this->fcpotransactionstatus__fcpo_txaction->value) {
            case 'cancelation':
                $sLangIdent = 'fcpo_receivable_cancelation';
                break;
            case 'appointed':
                if($dReceivable == 0) {
                    $sLangIdent = 'fcpo_receivable_appointed1';
                } elseif($dReceivable > 0) {
                    $sLangIdent = 'fcpo_receivable_appointed2';
                }
                break;
            case 'capture':
                $sLangIdent = 'fcpo_receivable_capture';
                break;
            case 'refund':
            case 'debit':
                if($dReceivable > 0) {
                    $sLangIdent = 'fcpo_receivable_debit1';
                } elseif($dReceivable < 0) {
                    $sLangIdent = 'fcpo_receivable_debit2';
                }
                break;
            case 'reminder':
                if($dReceivable == 0) {
                    $sLangIdent = 'fcpo_receivable_reminder';
                }
                break;
            default:
                $sLangIdent = 'FCPO_RECEIVABLE';
                break;
        }
        $oLang = oxLang::getInstance();
        return $oLang->translateString($sLangIdent,null,true);
    }

    /**
     * Get translated name of the payment action by payed money amount
     * 
     * @param double $dPayment payed amount
     * 
     * @return string
     */
    public function getDisplayNamePayment($dPayment) {
        switch ($this->fcpotransactionstatus__fcpo_txaction->value) {
            case 'capture':
                if($dPayment > 0) {
                    $sLangIdent = 'fcpo_payment_capture1';
                } elseif($dPayment < 0) {
                    $sLangIdent = 'fcpo_payment_capture2';
                }
                break;
            case 'cancelation':
            case 'paid':
                if($dPayment > 0) {
                    $sLangIdent = 'fcpo_payment_paid1';
                } elseif($dPayment < 0) {
                    $sLangIdent = 'fcpo_payment_paid2';
                }
                break;
            case 'underpaid':
                if($dPayment > 0) {
                    $sLangIdent = 'fcpo_payment_underpaid1';
                } elseif($dPayment < 0) {
                    $sLangIdent = 'fcpo_payment_underpaid2';
                }
                break;
            case 'refund':
            case 'debit':
                if($dPayment > 0) {
                    $sLangIdent = 'fcpo_payment_debit1';
                } elseif($dPayment < 0) {
                    $sLangIdent = 'fcpo_payment_debit2';
                }
                break;
            case 'transfer':
                $sLangIdent = 'fcpo_payment_transfer';
                break;
            default:
                $sLangIdent = 'fcpo_payment';
                break;
        }
        $oLang = oxLang::getInstance();
        return $oLang->translateString($sLangIdent,null,true);
    }

}