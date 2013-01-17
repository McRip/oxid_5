<?php

$sLangName  = "English";
// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(
'charset'                                   => 'ISO-8859-1',
'fcpo_admin_title'                          => 'PAYONE',
'fcpo_main_title'                           => 'Configuration',
'fcpo_main_log'                             => 'Transactions',
'FCPO_MERCHANT_ID'                          => 'PAYONE Merchant ID',
'FCPO_PORTAL_ID'                            => 'PAYONE Portal ID',
'FCPO_PORTAL_KEY'                           => 'PAYONE Portal Key',
'FCPO_OPERATION_MODE'                       => 'PAYONE Operation mode',
'FCPO_BONI_OPERATION_MODE'                  => 'Operation mode',
'FCPO_SUBACCOUNT_ID'                        => 'PAYONE Sub-Account ID',
'FCPO_BANKACCOUNTCHECK'                     => 'Check bank account',
'FCPO_DEACTIVATED'                          => 'Inactive',
'FCPO_ACTIVATED'                            => 'Active',
'FCPO_ACTIVATEDWITHPOS'                     => 'Active, with check against POS-CRL<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Only paymment method for germany)',
'FCPO_LIVE_MODE'                            => 'Live mode',
'FCPO_TEST_MODE'                            => 'Test mode',
'fcpo_order_title'                          => 'PAYONE',
'FCPO_REFNR'                                => 'Reference number',
'FCPO_TXID'                                 => 'PAYONE-Transaction Number (txid)',
'fcpo_action_appointed'                     => 'Order',
'fcpo_action_capture'                       => 'Capture',
'fcpo_action_paid'                          => 'Paid',
'fcpo_action_underpaid'                     => 'Underpaid',
'fcpo_action_overpaid'                      => '<span style="color: red;">Overpaid</span>',
'fcpo_action_cancelation'                   => 'Cancellation',
'fcpo_action_refund'                        => 'Refund',
'fcpo_action_debit'                         => 'Demand/Credit',
'fcpo_action_transfer'                      => 'Transfer',
'fcpo_action_reminder'                      => 'State dunning',
'fcpo_clearingtype_elv'                     => 'Direct Debit',
'fcpo_clearingtype_cc'                      => 'Credit Card',
'fcpo_clearingtype_vor'                     => 'Payadvance',
'fcpo_clearingtype_rec'                     => 'Bill',
'fcpo_clearingtype_cod'                     => 'Cash on delivery',
'fcpo_clearingtype_sb'                      => 'Online-Transfer',
'fcpo_clearingtype_wlt'                     => 'e-Wallet',
'fcpo_clearingtype_fnc'                     => 'Financing',
'fcpo_clearingtype_fcpobillsafe'            => 'BillSAFE',
'fcpo_clearingtype_fcpocommerzfinanz'       => 'Commerz Finanz',
'FCPO_CAPTURE_APPROVED'                     => 'Booking was successfull',
'FCPO_CAPTURE_ERROR'                        => 'Error occured during booking: ',
'FCPO_DEBIT_APPROVED'                       => 'Credit was successfull',
'FCPO_DEBIT_ERROR'                          => 'Error occured during credit: ',
'FCPO_LIST_HEADER_TXTIME'                   => 'Timestamp',
'FCPO_LIST_HEADER_ORDERNR'                  => 'Order Nr.',
'FCPO_LIST_HEADER_TXID'                     => 'Transaction number',
'FCPO_LIST_HEADER_CLEARINGTYPE'             => 'Payment method',
'FCPO_LIST_HEADER_EMAIL'                    => 'Customer e-mail',
'FCPO_LIST_HEADER_PRICE'                    => 'Price amount',
'FCPO_LIST_HEADER_TXACTION'                 => 'State',
'FCPO_EXECUTE'                              => 'Execute',
'FCPO_CAPTURE'                              => 'Capture ',
'FCPO_DEBIT'                                => 'Debit ',
'FCPO_ARE_YOU_SURE'                         => 'Are you sure that you want to perform this action?',
'FCPO_DE'                                   => 'Germany',
'FCPO_AT'                                   => 'Austria',
'FCPO_NL'                                   => 'Netherlands',
'FCPO_HEADER_BANKACCOUNT'                   => 'Bankaccount (optional)',
'FCPO_BANKCOUNTRY'                          => 'Account country',
'FCPO_BANKACCOUNT'                          => 'Account number',
'FCPO_BANKCODE'                             => 'Bank code number',
'FCPO_BANKACCOUNTHOLDER'                    => 'Account owner',
'FCPO_SHOW'                                 => 'show',
'FCPO_HIDE'                                 => 'hide',
'FCPO_PAYMENTTYPE'                          => 'Payment type',
'FCPO_CARDEXPIREDATE'                       => 'Expiredate',
'FCPO_CARDTYPE'                             => 'Card type',
'FCPO_CARDPAN'                              => 'Masked Card Number',
'FCPO_BALANCE'                              => 'Balance',
'FCPO_RECEIVABLE'                           => 'Payment',
'FC_IS_PAYONE'								=> 'This is a PAYONE payment method',
'FCPO_HELP_MERCHANTID'                      => 'You will find your PAYONE Merchant-ID on each invoice of PAYONE as well as on the right upper corner of the PAYONE Merchant Interface (PMI).',
'FCPO_HELP_PORTALID'                        => 'Please enter PAYONE Portal-ID which is used to complete payments.<br>You will find your Portal-ID at <a href="http://www.payone.de" target="_blank">http://www.payone.de</a> > Merchant-Login at menu entry Configuration > Payment Portals<br><br>You will get all relevant configuration parameters [edit] under tab [API-Parameter]',
'FCPO_HELP_PORTALKEY'                       => 'Please enter the Key that is used for secure transaction . This can be freely configured by you in PAYONE Portal interface.<br>You can find this configuration at <a href="http://www.payone.de" target="_blank">http://www.payone.de</a> > Merchant-Login at menu entry Configuration > Payment Portals > [editieren] > Reiter [Erweitert] > Key<br><br>You will get all relevant configuration parameters under tab [API-Parameter]',
'FCPO_HELP_OPERATIONMODE'                   => 'Hier k�nnen Sie f�r diese Zahlungsart festlegen ob die Zahlungen im Testmodus abgewickelt werden, oder ob diese Live ausgef�hrt werden. Bitte beachten Sie, dass f�r den Testmodus die definierten Testdaten verwendet werden m�ssen.',
'FCPO_HELP_OPERATIONMODE'					=> 'Here you can configure if the payments are processed in test mode, or whether they are performed live. Please note that for using the test mode, usage of defined test data si required.',
'FCPO_HELP_SUBACCOUNTID'                    => 'Please enter the ID of the Sub-Account, which will be processed through the payments and allocated. <br> The ID can be found at <a href = "http://www.payone.de" target = "_blank" > http://www.payone.de </ a>> Merchant Login in the menu Settings> Accounts <br> All relevant parameters for configuration, please visit <a href = "http://www.payone . de [edit] "target =" _blank "> http://www.payone.de </ a>> Merchant Login in the menu configuration> Payment portals>> tab [API parameters]',
'FCPO_HELP_POSCHECK'                        => 'Here you can define whether a check should be carried out by the bank against the POS-lock file. Please note that is done the module "Protect" must have been commissioned and the trial only for the direct debit payment Germany.',
'fcpo_admin_config'                         => 'Configuration',
'fcpo_admin_config_payment'                 => 'Payment Settings',
'fcpo_admin_protocol'                       => 'Protocols / Logs',
'FCPO_NO_TRANSACTION'                       => 'No transaction selected',
'fcpo_admin_information'                    => 'Information',
'fcpo_admin_common'                         => 'General',
'fcpo_admin_support'                        => 'Support',
'fcpo_admin_api_logs'                       => 'API Logs',
'FCPO_LIST_HEADER_TIMESTAMP'                => 'Time',
'FCPO_LIST_HEADER_REQUEST'                  => 'Request',
'FCPO_LIST_HEADER_RESPONSE'                 => 'Response',
'FCPO_NO_APILOG'                            => 'No log entry selected',
'FCPO_ACTIVE_CREDITCARD_TYPES'              => 'Active credit card brands',
'FCPO_CREDITCARDBRANDS_INFOTEXT'            => 'Here, the individual credit card brands, for the payment method to enable and configure credit card & Returns Please note that were the respective credit card brand in PAYONE charge must be <br> The setting for the payment by credit card you take with PAYONE -> Configuration -> payment before.',
'FCPO_ACTIVE_ONLINE_UBERWEISUNG_TYPES'      => 'Active online payment types',
'FCPO_ONLINEUBERWEISUNG_INFOTEXT'           => 'Here you can enable/disable the individual types of online money transfer and are able to configure the online payments as well as referral. <br> Please note that these payment methods have to be ordered individually before. <br> The settings for accepting online transfers in PAYONE  can be found at PAYONE -> Configuration -> payment types in the PAYONE merchant interface.',
'FCPO_CHANNEL'                              => 'Channel',
'FCPO_AUTHORIZATION_METHOD'                 => 'Authorize-Method',
'FCPO_PREAUTHORIZATION'                     => 'Preauthorization',
'FCPO_PREAUTHORIZATION_HELP'                => 'When you select "Preauthorization" the payable amount will be reserved as part of the order [recommended by PAYONE]. The charge (capture) must be initiated in a second step in before delivering the goods.',
'FCPO_AUTHORIZATION'                        => 'Authorize',
'FCPO_AUTHORIZATION_HELP'                   => 'When choosing "authorization", the amount to be paid will be charged immediately during the order.',
'dyn_fcpayone'                              => 'PAYONE',
'FCPO_ONLY_PAYONE'                          => 'Only PAYONE',
'ORDER_LIST_YOUWANTTOSTORNO'                => 'Do you really want to cancel this order?\n CAUTION: Eventually open PAYONE transactions should be completed before performing this action.',
'FCPO_ORDER_LIST_YOUWANTTODELETE'           => 'Do you really want to delete this order?\n \n CAUTION: Eventually open PAYONE transactions should be completed before deleting this order.',
'fcpo_admin_config_bonicheck'               => 'Protect',
'FCPO_ADDRESSCHECKTYPE'                     => 'Addresscheck',
'FCPO_NO_ADDRESSCHECK'                      => 'Do not perform addresscheck',
'FCPO_BASIC_ADDRESSCHECK'                   => 'AddressCheck Basic',
'FCPO_PERSON_ADDRESSCHECK'                  => 'AddressCheck Person',
'FCPO_HELP_NO_ADDRESSCHECK'                 => 'Deactivation of address check',
'FCPO_HELP_BASIC_ADDRESSCHECK'              => 'Check the address on existence and supplementing and correcting the address (Possible for addresses in Germany, Austria, Switzerland, Netherlands, Belgium, Luxembourg, France, Italy, Spain, Portugal, Denmark, Sweden, Finland, Norway, Poland, Slovakia, Czech Republic, Hungary, U.S., Canada)',
'FCPO_HELP_PERSON_ADDRESSCHECK'             => 'Check whether the person is known by the specified address, check the address on existence and supplementing and correcting the address (only Germany)',
'FCPO_CONSUMERSCORETYPE'                    => 'Consumerscore check',
'FCPO_NO_BONICHECK'                         => 'Do not perform comsumer score check',
'FCPO_HARD_BONICHECK'                       => 'Infoscore (Hard features)',
'FCPO_ALL_BONICHECK'                        => 'Infoscore (All features)',
'FCPO_ALL_SCORE_BONICHECK'                  => 'Infoscore (All features + Consumerscore)',
'FCPO_HELP_NO_BONICHECK'                    => 'Deactivation of the credit assessment',
'FCPO_HELP_HARD_BONICHECK'                  => 'Testing for so-called "hard" negative features (eg consumer insolvency proceedings, the arrest warrant affidavit, or enforcement of the tax affidavits). The credit check only supports the testing of buyers from Germany.',
'FCPO_HELP_ALL_BONICHECK'                   => 'Testing for so-called "hard" negative features (eg consumer insolvency proceedings, arrest warrant for the affidavit or enforce submission of the affidavit), "medium" negative features (eg court order, enforcement order or enforcement) and "soft" negative features (such as collection agency dunning initiated Continuing the extrajudicial debt collection payment procedure for partial payment, setting the order for payment out of court debt collection because of hopelessness). The credit check only supports the testing of buyers from Deutschland.Testing for so-called "hard" negative features (eg consumer insolvency proceedings, the arrest warrant affidavit, or enforcement of the tax affidavits). The credit check only supports the testing of buyers from Germany.',
'FCPO_HELP_ALL_SCORE_BONICHECK'             => 'Testing for so-called "hard" negative features (eg consumer insolvency proceedings, arrest warrant for the affidavit or enforce submission of the affidavit), "medium" negative features (eg court order, enforcement order or enforcement) and "soft" negative features (such as collection agency dunning initiated Continuing the extrajudicial debt collection payment procedure for partial payment, setting the order for payment out of court debt collection because of hopelessness). The credit check only supports the testing of buyers from Germany. <br> Bonuses The core is a score value and allows a higher selectivity for these negative characteristics.',
'FCPO_HELP_BONI_OPERATIONMODE'              => 'Here you can set the credit check if the checks are processed in test mode, or if they have to be executed in live mode.',
'FCPO_SEND_ARTICLELIST'                     => 'Send article list',
'FCPO_HELP_SEND_ARTICLELIST'                => 'When activated the proposed requests will be sent to the system of PAYONE including the individual prices of items shipped. <br> This option must be checked if they have activated the PAYONE invoicing.',
'FCPO_CHECK_DEL_ADDRESS'                    => 'Check delivery address',
'FCPO_HELP_CHECK_DEL_ADDRESS'               => 'Additional delivery address check through PAYONE address validation.',
'FCPO_CORRECT_ADDRESS'                      => 'Commit corrected addresses',
'FCPO_HELP_CORRECT_ADDRESS'                 => 'Acquisition of each corrected address validation instead of the address which was entered your shop.',
'FCPO_STATUS_WITH_USER_CORRECTION'          => 'User will be sent back to user form, if:',
'FCPO_ADDRESSCHECK_PPB'                     => 'Name and family name is known',
'FCPO_ADDRESSCHECK_PHB'                     => 'Family name is known',
'FCPO_ADDRESSCHECK_PAB'                     => 'Name and family name is not known',
'FCPO_ADDRESSCHECK_PKI'                     => 'Ambiguity in name to address',
'FCPO_ADDRESSCHECK_PNZ'                     => '(no longer) undeliverable',
'FCPO_ADDRESSCHECK_PPV'                     => 'Person died',
'FCPO_ADDRESSCHECK_PPF'                     => 'If postal address is incorrect, users will be sent back to the user form.',
'FCPO_DURABILITY_BONICHECK'                 => 'Lifetime credit check in days',
'FCPO_HELP_DURABILITY_BONICHECK'            => 'Number of days after which a new credit check is performed. <br> Please note the provisions of the Data Protection Act and the terms and conditions regarding the storage and the life of the credit checks. It is recommended to configure a service life of 1 day.',
'FCPO_MODULE_VERSION'                       => 'Module Version',
'FCPO_STARTLIMIT_BONICHECK'                 => 'Credit check up from minimum value of goods',
'FCPO_HELP_STARTLIMIT_BONICHECK'            => 'Credit check is performed only if the value is higher than the configured value here. <br> If the credit check should always be carried out, leave it blank.',
'FCPO_HELP_ASSIGNCOUNTRIES'                 => 'If no countries are assigned, the payment applies to all countries. <br> If countries are assigned, the payment applies only to these countries. <br> Billing country and destination country weill be tested.',
'fcpo_receivable_appointed1'                => 'Reservation',
'fcpo_receivable_appointed2'                => 'Demand (Authorazation)',
'fcpo_receivable_capture'                   => 'Demand (Capture)',
'fcpo_receivable_debit1'                    => 'Demand (Debit)',
'fcpo_receivable_debit2'                    => 'Credit (Debit/Refund)',
'fcpo_receivable_reminder'                  => 'Reminder delivery',
'fcpo_receivable_cancelation'               => 'Chargeback fee',
'fcpo_payment_capture1'                     => 'Indent',
'fcpo_payment_capture2'                     => 'Payout',
'fcpo_payment_paid1'                        => 'Receipt of payment',
'fcpo_payment_paid2'                        => 'Chargeback',
'fcpo_payment_underpaid1'                   => 'Underpaid',
'fcpo_payment_underpaid2'                   => 'Chargeback',
'fcpo_payment_debit1'                       => 'Indent',
'fcpo_payment_debit2'                       => 'Payout',
'fcpo_payment_transfer'                     => 'Rebooking',
'fcpo_payment'                              => 'Payment',
'FCPO_MAIN_CONFIG_INFOTEXT'                 => 'You can configure individually for each payment type, whether it should be handled in test or live mode. You can set them at PAYONE -> Configuration -> Payment Methods. We recommend following the initial configuration in test mode and switch over to live mode if everything works as expected.',
'FCPO_BONICHECK_CONFIG_INFOTEXT'            => 'Please note that you can use the following options only if you have activated the Protect module of PAYONE. The use of credit checks and address verification takes variable costs per transaction by itself, which you can refer to your contract.',
'FCPO_BONICHECK_CONFIG_INFOTEXT_SMALL'      => 'Please adjust the settings for the credit check carefully. The credit check is performed by entering the personal data and affects the payment methods that your customers will be offered in the checkout process. The credit rating should be used only for payment methods that result in a payment default risk for you by itself (eg, open account or debit). configure this setting via the "credit index" in the configuration of the respective method of payment. should also indicate in your store in an appropriate way that you perform credit checks on the infoscore Consumer Data GmbH.',
'FCPO_INFOTEXT_SET_OPERATIONMODE'           => 'Will be set manually at PAYONE->Configuration->payment settings',
'FCPO_DEFAULT_BONI'                         => 'Standard credit index',
'FCPO_HELP_DEFAULT_BONI'                    => 'This credit index, the customer receives when registering <br> Purpose:. If the customer has not yet been tested and the test is done only at a certain value, this is the index of credit until the first real test will be considered <br. > <br> If this field is left blank the OXID standard is set (1000).',
'FCPO_SETTLE_ACCOUNT'                       => 'Perform settlement',
'FCPO_HELP_SETTLE_ACCOUNT'                  => 'Disable checkbox for partial "Perform settlement" . Be activated during the last partial delivery has this option to perform a balancing accounts. Please note that this function only for ELV, in advance, online payment and billing is available. Also this feature for credit card prior to activation of PAYONE is available.',
'FCPO_PRESAVE_ORDER'                        => 'Save order before authorization',
'FCPO_REDUCE_STOCK'                         => 'Reduce stock',
'FCPO_HELP_REDUCE_STOCK'                    => 'This configuration only has effect when "Save order before authorization" is activated and the customer is redirected to an external paymentservice ( i.e. Sofort�berweisung, PayPal or creditcard with 3D Secure ). This configuration defines if the stock for the articles is reduced before or after the redirect to the external paymentservice.',
'FCPO_REDUCE_STOCK_BEFORE'                  => 'before authorization',
'FCPO_REDUCE_STOCK_AFTER'                   => 'after authorization',
'FCPO_HELP_PRESAVE_ORDER'                   => 'The order is saved before the authorization als incomplete Order, so that there is a order-number which can be sent to Payone.',
'FCPO_VOUCHER'                              => 'voucher',
'FCPO_DISCOUNT'                             => 'discount',
'FCPO_WRAPPING'                             => "Gift Wrapping/Greeting Card",
'FCPO_SURCHARGE'                            => 'Surcharge',
'FCPO_DEDUCTION'                            => 'Deduction',
'FCPO_PAYMENTTYPE'                          => "Type of Payment:",
'FCPO_SHIPPINGCOST'                         => "Shipping cost",
'FCPO_PRODUCT_CAPTURE'                      => "Capture",
'FCPO_PRODUCT_AMOUNT'                       => "Amount",
'FCPO_PRODUCT_PRICE'                        => "Unit price",
'FCPO_PRODUCT_TITLE'                        => "Product",
'FCPO_COMPLETE_ORDER'                       => "Complete order",
);

/*
[{ oxmultilang ident="GENERAL_YOUWANTTODELETE" }]
*/
