<?php

/**
 * Module information
 */
$aModule = array(
    'id'            => 'fcpayone',
    'title'         => 'PAYONE FinanceGate',
    'description'   => 'PAYONE bietet Ihnen mit dem Payment-Modul f&uuml;r Oxid eShops &uuml;ber 20 Zahlarten aus einer Hand. Das Payment-Modul von PAYONE bietet eine nahtlose Integration in den Checkout-Prozess des OXID eShops.',
    'thumbnail'     => 'picture.gif',
    'version'       => '1.4',
    'author'        => 'FATCHIP GmbH',
    'email'         => 'kontakt@fatchip.de',
    'url'           => 'http://wiki.fatchip.de/fc/mod_oxid_payone/start',
    'events'       => array(
        'onActivate'   => 'rm_packstation_events::onActivate',
        'onDeactivate' => 'rm_packstation_events::onDeactivate',
    ),
    'extend'        => array(
        'oxbasketitem'              => 'fcPayOne/core/fcPayOneBasketitem',
        'oxorder'                   => 'fcPayOne/core/fcPayOneOrder',
		'oxorderarticle'            => 'fcPayOne/core/fcPayOneOrderarticle',
        'oxpayment'                 => 'fcPayOne/core/fcPayOnePayment',
        'oxpaymentgateway'          => 'fcPayOne/core/fcPayOnePaymentgateway',
        'oxuser'                    => 'fcPayOne/core/fcPayOneUser',
        'payment'                   => 'fcPayOne/controllers/fcPayOnePaymentView',
        'roles_bemain'              => 'fcPayOne/controllers/admin/fcPayOneRolesBeMain',
    ),
    'files'         => array(
        'fcPayOne_Main_Ajax'        => 'fcPayOne/controllers/admin/fcPayOne_Main_Ajax.php',
        'fcpayone_admin'            => 'fcPayOne/controllers/admin/fcpayone_admin.php',
        'fcpayone_apilog'           => 'fcPayOne/controllers/admin/fcpayone_apilog.php',
        'fcpayone_apilog_list'      => 'fcPayOne/controllers/admin/fcpayone_apilog_list.php',
        'fcpayone_apilog_main'      => 'fcPayOne/controllers/admin/fcpayone_apilog_main.php',
        'fcpayone_boni'             => 'fcPayOne/controllers/admin/fcpayone_boni.php',
        'fcpayone_boni_list'        => 'fcPayOne/controllers/admin/fcpayone_boni_list.php',
        'fcpayone_boni_main'        => 'fcPayOne/controllers/admin/fcpayone_boni_main.php',
        'fcpayone_common'           => 'fcPayOne/controllers/admin/fcpayone_common.php',
        'fcpayone_list'             => 'fcPayOne/controllers/admin/fcpayone_list.php',
        'fcpayone_log'              => 'fcPayOne/controllers/admin/fcpayone_log.php',
        'fcpayone_log_list'         => 'fcPayOne/controllers/admin/fcpayone_log_list.php',
        'fcpayone_main'             => 'fcPayOne/controllers/admin/fcpayone_main.php',
        'fcpayone_order'            => 'fcPayOne/controllers/admin/fcpayone_order.php',
        'fcpayone_protocol'         => 'fcPayOne/controllers/admin/fcpayone_protocol.php',
        'fcpayone_support'          => 'fcPayOne/controllers/admin/fcpayone_support.php',
        'fcpayone_support_list'     => 'fcPayOne/controllers/admin/fcpayone_support_list.php',
        'fcpayone_support_main'     => 'fcPayOne/controllers/admin/fcpayone_support_main.php',
        'fcpoRequest'               => 'fcPayOne/models/fcporequest.php',
        'fcporequestlog'            => 'fcPayOne/models/fcporequestlog.php',
        'fcpoTransactionStatus'     => 'fcPayOne/models/fcpotransactionstatus.php',
        'fcpoSetup'                 => 'fcPayOne/fcpoSetup.php'
    ),
    'templates' => array(
        'fcpayone.tpl' => 'fcPayOne/views/admin/tpl/fcpayone.tpl',
        'fcpayone_apilog.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_apilog.tpl',
        'fcpayone_apilog_list.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_apilog_list.tpl',
        'fcpayone_apilog_main.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_apilog_main.tpl',
        'fcpayone_boni.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_boni.tpl',
        'fcpayone_boni_list.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_boni_list.tpl',
        'fcpayone_boni_main.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_boni_main.tpl',
        'fcpayone_common.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_common.tpl',
        'fcpayone_list.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_list.tpl',
        'fcpayone_log.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_log.tpl',
        'fcpayone_log_list.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_log_list.tpl',
        'fcpayone_main.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_main.tpl',
        'fcpayone_order.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_order.tpl',
        'fcpayone_protocol.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_protocol.tpl',
        'fcpayone_support.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_support.tpl',
        'fcpayone_support_list.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_support_list.tpl',
        'fcpayone_support_main.tpl' => 'fcPayOne/views/admin/tpl/fcpayone_support_main.tpl',
        'popups/fcpayone_main.tpl' => 'fcPayOne/views/admin/tpl/popups/fcpayone_main.tpl'
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

$sShopEdition = oxRegistry::getConfig()->getActiveShop()->oxshops__oxedition->value;
if($sShopEdition == 'EE') {
    $aModule['blocks'][] = array(
            'template' => 'roles_bemain.tpl',
            'block' => 'admin_roles_bemain_form',
            'file' => 'fcpo_admin_roles_bemain_form',
    );
}