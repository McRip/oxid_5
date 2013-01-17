[{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance"}]
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{$shop->oxshops__oxbankname->getRawValue()}]<br>
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ROUTINGNOMBER" }] [{$shop->oxshops__oxbankcode->value}]<br>
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ACCOUNTNOMBER" }] [{$shop->oxshops__oxbanknumber->value}]<br>
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BIC" }] [{$shop->oxshops__oxbiccode->value}]<br>
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_IBAN" }] [{$shop->oxshops__oxibannumber->value}]
[{* FCPAYONE BEGIN *}]
[{elseif $payment->oxuserpayments__oxpaymentsid->value == "fcpopayadvance" }]
[{ oxmultilang ident="FCPO_BANKACCOUNTHOLDER" }] [{ $order->getFcpoBankaccountholder() }]
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{ $order->getFcpoBankname() }]
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ROUTINGNOMBER" }] [{ $order->getFcpoBankcode() }]
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ACCOUNTNOMBER" }] [{ $order->getFcpoBanknumber() }]
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BIC" }] [{ $order->getFcpoBiccode() }]
[{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_IBAN" }] [{ $order->getFcpoIbannumber() }]
[{* FCPAYONE END *}]
[{else}]
[{ $smarty.block.parent }]
[{/if}]