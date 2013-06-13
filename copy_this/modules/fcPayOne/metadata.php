<?php

/**
 * Module information
 */
$aModule = array(
    'id'            => 'fcpayone',
    'title'         => 'PAYONE FinanceGate',
    'description'   => 'PAYONE bietet Ihnen mit dem Payment-Modul f&uuml;r Oxid eShops &uuml;ber 20 Zahlarten aus einer Hand. Das Payment-Modul von PAYONE bietet eine nahtlose Integration in den Checkout-Prozess des OXID eShops.',
    'thumbnail'     => 'picture.gif',
    'version'       => '1.3.5_2664',
    'author'        => 'FATCHIP GmbH',
    'email'         => 'kontakt@fatchip.de',
    'url'           => 'http://wiki.fatchip.de/fc/mod_oxid_payone/start',
    'extend'        => array(
        'oxbasketitem' => 'fcPayOne/core/fcPayOneBasketitem',
        'oxorder' => 'fcPayOne/core/fcPayOneOrder',
		'oxorderarticle' => 'fcPayOne/core/fcPayOneOrderarticle',
        'oxpayment' => 'fcPayOne/core/fcPayOnePayment',
        'oxpaymentgateway' => 'fcPayOne/core/fcPayOnePaymentgateway',
        'oxuser' => 'fcPayOne/core/fcPayOneUser',
        'payment' => 'fcPayOne/views/fcPayOnePaymentView',
        'roles_bemain' => 'fcPayOne/admin/fcPayOneRolesBeMain',
    ),
    'files'         => array(
        'fcPayOne_Main_Ajax' => 'fcPayOne/admin/fcPayOne_Main_Ajax.php',
    ),
    'blocks'        => array(
        array(
            'template' => 'page/checkout/payment.tpl',
            'block' => 'change_payment',
            'file' => 'fcpo_payment_override',
        ),
        array(
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => 'fcpo_payment_select_override',
        ),
        array(
            'template' => 'email/html/order_cust.tpl',
            'block' => 'email_html_order_cust_paymentinfo',
            'file' => 'fcpo_email_html_order_cust_paymentinfo',
        ),
        array(
            'template' => 'email/plain/order_cust.tpl',
            'block' => 'email_plain_order_cust_paymentinfo',
            'file' => 'fcpo_email_plain_order_cust_paymentinfo',
        ),
        array(
            'template' => 'order_list.tpl',
            'block' => 'admin_order_list_colgroup',
            'file' => 'fcpo_admin_order_list_colgroup',
        ),
        array(
            'template' => 'order_list.tpl',
            'block' => 'admin_order_list_filter',
            'file' => 'fcpo_admin_order_list_filter',
        ),
        array(
            'template' => 'order_list.tpl',
            'block' => 'admin_order_list_sorting',
            'file' => 'fcpo_admin_order_list_sorting',
        ),
        array(
            'template' => 'order_list.tpl',
            'block' => 'admin_order_list_item',
            'file' => 'fcpo_admin_order_list_item',
        ),
        array(
            'template' => 'payment_list.tpl',
            'block' => 'admin_payment_list_filter',
            'file' => 'fcpo_admin_payment_list_filter',
        ),
        array(
            'template' => 'payment_main.tpl',
            'block' => 'admin_payment_main_form',
            'file' => 'fcpo_admin_payment_main_form',
        ),
    ),
);

$sShopEdition = oxConfig::getInstance()->getActiveShop()->oxshops__oxedition->value;
if($sShopEdition == 'EE') {
    $aModule['blocks'][] = array(
            'template' => 'roles_bemain.tpl',
            'block' => 'admin_roles_bemain_form',
            'file' => 'fcpo_admin_roles_bemain_form',
    );
}