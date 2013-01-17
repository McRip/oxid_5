[{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance" || $payment->oxuserpayments__oxpaymentsid->value == "fcpopayadvance"}]
    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
        [{ oxmultilang ident="BANK_DETAILS" }]
    </h3>
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
        [{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance"}]
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{$shop->oxshops__oxbankname->value}]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ROUTINGNOMBER" }] [{$shop->oxshops__oxbankcode->value}]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ACCOUNTNOMBER" }] [{$shop->oxshops__oxbanknumber->value}]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BIC" }] [{$shop->oxshops__oxbiccode->value}]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_IBAN" }] [{$shop->oxshops__oxibannumber->value}]
        <!-- FCPAYONE BEGIN -->
        [{elseif $payment->oxuserpayments__oxpaymentsid->value == "fcpopayadvance"}]
            [{ oxmultilang ident="FCPO_BANKACCOUNTHOLDER" }] [{ $order->getFcpoBankaccountholder() }]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{ $order->getFcpoBankname() }]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ROUTINGNOMBER" }] [{ $order->getFcpoBankcode() }]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_ACCOUNTNOMBER" }] [{ $order->getFcpoBanknumber() }]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BIC" }] [{ $order->getFcpoBiccode() }]<br>
            [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_IBAN" }] [{ $order->getFcpoIbannumber() }]
        <!-- FCPAYONE END -->
        [{/if}]
    </p>
[{else}]
    [{ $smarty.block.parent }]
[{/if}]