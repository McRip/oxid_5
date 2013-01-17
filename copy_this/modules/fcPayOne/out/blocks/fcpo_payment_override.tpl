[{oxscript include="fcPayOne.js"}]
<script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>
<style type="text/css">
    .fcpo_check_error, #fcpo_elv_error, #fcpo_cc_error, #fcpo_ou_error {
        display: none;
    }

    .errorbox {
         background-color: red;
         color: white;
    }
</style>

[{oxscript include="js/widgets/oxpayment.js" priority=10 }]
[{oxscript add="$( '#payment' ).oxPayment();"}]
[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{capture name="oxValidate"}]
if (!((document.all && !document.querySelector) || (document.all && document.querySelector && !document.addEventListener))) {
    $('form.js-oxValidate').oxInputValidator();
}
[{/capture}]
[{oxscript add=$smarty.capture.oxValidate}]
<form action="[{ $oViewConf->getSslSelfLink() }]" class="js-oxValidate payment" id="payment" name="order" method="post" onsubmit="return fcCheckPaymentSelection();">
    <div>
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
        <input type="hidden" name="fnc" value="validatepayment">

        <input type="hidden" name="fcpo_mid" value="[{$oView->getMerchantId()}]">
        <input type="hidden" name="fcpo_portalid" value="[{$oView->getPortalId()}]">
        <input type="hidden" name="fcpo_encoding" value="[{$oView->getEncoding()}]">
        <input type="hidden" name="fcpo_aid" value="[{$oView->getSubAccountId()}]">
        <input type="hidden" name="fcpo_amount" value="[{$oView->getAmount()}]">
        <input type="hidden" name="fcpo_currency" value="[{ $currency->name}]">
        <input type="hidden" name="fcpo_tpllang" value="[{$oView->getTplLang()}]">
        <input type="hidden" name="fcpo_bill_country" value="[{$oView->fcGetBillCountry()}]">
        <input type="hidden" name="dynvalue[fcpo_pseudocardpan]" value="">
        <input type="hidden" name="dynvalue[fcpo_ccmode]" value="">
        <input type="hidden" name="fcpo_checktype" value="[{$oView->getChecktype()}]">
        <input type="hidden" name="fcpo_hashelvWith" value="[{$oView->getHashELVWithChecktype()}]">
        <input type="hidden" name="fcpo_hashelvWithout" value="[{$oView->getHashELVWithoutChecktype()}]">

        <input type="hidden" name="fcpo_integratorid" value="[{$oView->getIntegratorid()}]">
        <input type="hidden" name="fcpo_integratorver" value="[{$oView->getIntegratorver()}]">
        <input type="hidden" name="fcpo_integratorextver" value="[{$oView->getIntegratorextver()}]">
    </div>

    [{if $oView->getPaymentList()}]
        <h3 id="paymentHeader" class="blockHead">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_PAYMENT" }]</h3>
        [{ assign var="inptcounter" value="-1"}]
        [{foreach key=sPaymentID from=$oView->getPaymentList() item=paymentmethod name=PaymentSelect}]
            [{ assign var="inptcounter" value="`$inptcounter+1`"}]
            [{block name="select_payment"}]
                [{if $sPaymentID == "oxidcashondel"}]
                    [{include file="page/checkout/inc/payment_oxidcashondel.tpl"}]
                [{elseif $sPaymentID == "oxidcreditcard"}]
                    [{include file="page/checkout/inc/payment_oxidcreditcard.tpl"}]
                [{elseif $sPaymentID == "oxiddebitnote"}]
                    [{include file="page/checkout/inc/payment_oxiddebitnote.tpl"}]
                [{elseif $sPaymentID == "fcpocreditcard"}]
                    [{if $oView->hasPaymentMethodAvailableSubTypes('cc') }]
                        [{ assign var="dynvalue" value=$oView->getDynValue()}]
                        <dl>
                            <dt>
                                <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
                                <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
                            </dt>
                            <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
                                <ul class="form">
                                    <li id="fcpo_cc_error">
                                        <div class="oxValidateError" style="display: block;padding: 0;">
                                            [{ oxmultilang ident="FCPO_ERROR" }]<div id="fcpo_cc_error_content"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_CREDITCARD" }]</label>
                                        <select name="dynvalue[fcpo_kktype]" [{if $oView->getMaestroUK() }]onchange="fcCheckType(this);return false;"[{/if}]>
                                            [{if $oView->getVisa() }]<option value="V" [{ if ($dynvalue.fcpo_kktype == "V" || !$dynvalue.fcpo_kktype)}]selected[{/if}]>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_VISA" }]</option>[{/if}]
                                            [{if $oView->getMastercard() }]<option value="M" [{ if $dynvalue.fcpo_kktype == "M"}]selected[{/if}]>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_MASTERCARD" }]</option>[{/if}]
                                            [{if $oView->getAmex() }]<option value="A" [{ if $dynvalue.fcpo_kktype == "A"}]selected[{/if}]>American Express</option>[{/if}]
                                            [{if $oView->getDiners() }]<option value="D" [{ if $dynvalue.fcpo_kktype == "D"}]selected[{/if}]>Diners Club</option>[{/if}]
                                            [{if $oView->getJCB() }]<option value="J" [{ if $dynvalue.fcpo_kktype == "J"}]selected[{/if}]>JCB</option>[{/if}]
                                            [{if $oView->getMaestroInternational() }]<option value="O" [{ if $dynvalue.fcpo_kktype == "O"}]selected[{/if}]>Maestro International</option>[{/if}]
                                            [{if $oView->getMaestroUK() }]<option value="U" [{ if $dynvalue.fcpo_kktype == "U"}]selected[{/if}]>Maestro UK</option>[{/if}]
                                            [{if $oView->getDiscover() }]<option value="C" [{ if $dynvalue.fcpo_kktype == "C"}]selected[{/if}]>Discover</option>[{/if}]
                                            [{if $oView->getCarteBleue() }]<option value="B" [{ if $dynvalue.fcpo_kktype == "B"}]selected[{/if}]>Carte Bleue</option>[{/if}]
                                        </select>
                                        [{if $oView->getVisa() }]
                                            <input type="hidden" name="fcpo_hashcc_V" value="[{$oView->getHashCC('V')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_V" value="[{$paymentmethod->fcpoGetOperationMode('V')}]">
                                        [{/if}]
                                        [{if $oView->getMastercard() }]
                                            <input type="hidden" name="fcpo_hashcc_M" value="[{$oView->getHashCC('M')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_M" value="[{$paymentmethod->fcpoGetOperationMode('M')}]">
                                        [{/if}]
                                        [{if $oView->getAmex() }]
                                            <input type="hidden" name="fcpo_hashcc_A" value="[{$oView->getHashCC('A')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_A" value="[{$paymentmethod->fcpoGetOperationMode('A')}]">
                                        [{/if}]
                                        [{if $oView->getDiners() }]
                                            <input type="hidden" name="fcpo_hashcc_D" value="[{$oView->getHashCC('D')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_D" value="[{$paymentmethod->fcpoGetOperationMode('D')}]">
                                        [{/if}]
                                        [{if $oView->getJCB() }]
                                            <input type="hidden" name="fcpo_hashcc_J" value="[{$oView->getHashCC('J')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_J" value="[{$paymentmethod->fcpoGetOperationMode('J')}]">
                                        [{/if}]
                                        [{if $oView->getMaestroInternational() }]
                                            <input type="hidden" name="fcpo_hashcc_O" value="[{$oView->getHashCC('O')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_O" value="[{$paymentmethod->fcpoGetOperationMode('O')}]">
                                        [{/if}]
                                        [{if $oView->getMaestroUK() }]
                                            <input type="hidden" name="fcpo_hashcc_U" value="[{$oView->getHashCC('U')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_U" value="[{$paymentmethod->fcpoGetOperationMode('U')}]">
                                        [{/if}]
                                        [{if $oView->getDiscover() }]
                                            <input type="hidden" name="fcpo_hashcc_C" value="[{$oView->getHashCC('C')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_C" value="[{$paymentmethod->fcpoGetOperationMode('C')}]">
                                        [{/if}]
                                        [{if $oView->getCarteBleue() }]
                                            <input type="hidden" name="fcpo_hashcc_B" value="[{$oView->getHashCC('B')}]">
                                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]_B" value="[{$paymentmethod->fcpoGetOperationMode('B')}]">
                                        [{/if}]
                                    </li>
                                    <li>
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_NUMBER" }]</label>
                                        <input type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[fcpo_kknumber]" value="[{ $dynvalue.fcpo_kknumber }]">
                                        <div id="fcpo_cc_number_invalid" class="fcpo_check_error">
                                            <p class="oxValidateError" style="display: block;">
                                                [{ oxmultilang ident="FCPO_CC_NUMBER_INVALID" }]
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_ACCOUNTHOLDER" }]</label>
                                        <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_kkname]" value="[{ if $dynvalue.fcpo_kkname }][{ $dynvalue.fcpo_kkname }][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]">
                                        <br>
                                        <div class="note">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_DIFFERENTBILLINGADDRESS" }]</div>
                                    </li>
                                    <li>
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_VALIDUNTIL" }]</label>
                                        <select name="dynvalue[fcpo_kkmonth]">
                                            <option [{ if $dynvalue.fcpo_kkmonth == "01"}]selected[{/if}]>01</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "02"}]selected[{/if}]>02</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "03"}]selected[{/if}]>03</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "04"}]selected[{/if}]>04</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "05"}]selected[{/if}]>05</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "06"}]selected[{/if}]>06</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "07"}]selected[{/if}]>07</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "08"}]selected[{/if}]>08</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "09"}]selected[{/if}]>09</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "10"}]selected[{/if}]>10</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "11"}]selected[{/if}]>11</option>
                                            <option [{ if $dynvalue.fcpo_kkmonth == "12"}]selected[{/if}]>12</option>
                                        </select>&nbsp;/&nbsp;

                                        <select name="dynvalue[fcpo_kkyear]">
                                            [{foreach from=$oView->getCreditYears() item=year}]
                                                <option [{ if $dynvalue.fcpo_kkyear == $year}]selected[{/if}]>[{$year}]</option>
                                            [{/foreach}]
                                        </select>
                                        <div id="fcpo_cc_date_invalid" class="fcpo_check_error">
                                            <p class="oxValidateError" style="display: block;">
                                                [{ oxmultilang ident="FCPO_CC_DATE_INVALID" }]
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_SECURITYCODE" }]</label>
                                        <input type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[fcpo_kkpruef]" value="[{ $dynvalue.fcpo_kkpruef }]">
                                        <div id="fcpo_cc_cvc2_invalid" class="fcpo_check_error">
                                            <p class="oxValidateError" style="display: block;">
                                                [{ oxmultilang ident="FCPO_CC_CVC2_INVALID" }]
                                            </p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="note">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_SECURITYCODEDESCRIPTION" }]</div>
                                    </li>
                                    [{if $oView->getMaestroUK() }]
                                        <li id="fcpo_kkcsn_row" style="display: none;">
                                            <label>[{ oxmultilang ident="FCPO_CARDSEQUENCENUMBER" }]</label>
                                            <input type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[fcpo_kkcsn]" value="[{ $dynvalue.fcpo_kkcsn }]">
                                        </li>
                                    [{/if}]
                                </ul>
                            </dd>
                        </dl>
                        <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
                        [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                            <div class="desc">
                                [{ $paymentmethod->oxpayments__oxlongdesc->value}]
                            </div>
                        [{/if}]
                    [{/if}]
                [{elseif $sPaymentID == "fcpodebitnote"}]
                    [{ assign var="dynvalue" value=$oView->getDynValue()}]
                    <dl>
                        <dt>
                            <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
                            <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
                        </dt>
                        <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
                            <ul class="form">
                                <li id="fcpo_elv_error">
                                    <div class="oxValidateError" style="display: block;padding: 0;">
                                        [{ oxmultilang ident="FCPO_ERROR" }]<div id="fcpo_elv_error_content"></div>
                                    </div>
                                </li>
                                <li>
                                    <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_ROUTINGNUMBER" }]</label>
                                    <input id="payment_[{$sPaymentID}]_1" type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_blz]" value="[{ $dynvalue.fcpo_elv_blz }]">
                                    <div id="fcpo_elv_blz_invalid" class="fcpo_check_error">
                                        <p class="oxValidateError" style="display: block;">
                                            [{ oxmultilang ident="FCPO_BLZ_INVALID" }]
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_ACCOUNTNUMBER" }]</label>
                                    <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_ktonr]" value="[{ $dynvalue.fcpo_elv_ktonr }]">
                                    <div id="fcpo_elv_ktonr_invalid" class="fcpo_check_error">
                                        <p class="oxValidateError" style="display: block;">
                                            [{ oxmultilang ident="FCPO_KTONR_INVALID" }]
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_ACCOUNTHOLDER2" }]</label>
                                    <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_ktoinhaber]" value="[{ if $dynvalue.fcpo_elv_ktoinhaber }][{ $dynvalue.fcpo_elv_ktoinhaber }][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]">
                                </li>
                            </ul>
                            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
                            [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                                <div class="desc">
                                    [{ $paymentmethod->oxpayments__oxlongdesc->value}]
                                </div>
                            [{/if}]
                        </dd>
                    </dl>
                [{elseif $sPaymentID == "fcpoonlineueberweisung"}]
                    [{if $oView->hasPaymentMethodAvailableSubTypes('sb')}]
                        [{ assign var="dynvalue" value=$oView->getDynValue()}]
                        <dl>
                            <dt>
                                <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
                                <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
                            </dt>
                            <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
                                <ul class="form" style="width:400px;">
                                    <li id="fcpo_ou_error">
                                        <div class="oxValidateError" style="display: block;padding: 0;">
                                            [{ oxmultilang ident="FCPO_ERROR" }]<div id="fcpo_ou_error_content"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <label>[{ oxmultilang ident="FCPO_ONLINE_UEBERWEISUNG_TYPE" }]</label>
                                        <select name="dynvalue[fcpo_sotype]" onchange="fcCheckOUType(this);return false;">
                                            [{if $oView->getSofortUeberweisung() }]<option value="PNT" [{ if ($dynvalue.fcpo_sotype == "PNT" || !$dynvalue.fcpo_sotype)}]selected[{/if}]>Sofort-&Uuml;berweisung</option>[{/if}]
                                            [{if $oView->getGiropay() }]<option value="GPY" [{ if $dynvalue.fcpo_sotype == "GPY"}]selected[{/if}]>giropay</option>[{/if}]
                                            [{if $oView->getEPS() }]<option value="EPS" [{ if $dynvalue.fcpo_sotype == "EPS"}]selected[{/if}]>eps - Online-&Uuml;berweisung</option>[{/if}]
                                            [{if $oView->getPostFinanceEFinance() }]<option value="PFF" [{ if $dynvalue.fcpo_sotype == "PFF"}]selected[{/if}]>PostFinance E-Finance</option>[{/if}]
                                            [{if $oView->getPostFinanceCard() }]<option value="PFC" [{ if $dynvalue.fcpo_sotype == "PFC"}]selected[{/if}]>PostFinance Card</option>[{/if}]
                                            [{if $oView->getIdeal() }]<option value="IDL" [{ if $dynvalue.fcpo_sotype == "IDL"}]selected[{/if}]>iDeal</option>[{/if}]
                                        </select>
                                    </li>
                                    <li id="fcpo_ou_blz">
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_ROUTINGNUMBER" }]</label>
                                        <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_ou_blz]" value="[{ $dynvalue.fcpo_ou_blz }]">
                                        <div id="fcpo_ou_blz_invalid" class="fcpo_check_error">
                                            <p class="oxValidateError" style="display: block;">
                                                [{ oxmultilang ident="FCPO_BLZ_INVALID" }]
                                            </p>
                                        </div>
                                    </li>
                                    <li id="fcpo_ou_ktonr">
                                        <label>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_ACCOUNTNUMBER" }]</label>
                                        <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_ou_ktonr]" value="[{ $dynvalue.fcpo_ou_ktonr }]">
                                        <div id="fcpo_ou_ktonr_invalid" class="fcpo_check_error">
                                            <p class="oxValidateError" style="display: block;">
                                                [{ oxmultilang ident="FCPO_KTONR_INVALID" }]
                                            </p>
                                        </div>
                                    </li>
                                    <li id="fcpo_ou_eps" style="display: none;width: 400px;">
                                        <label>[{ oxmultilang ident="FCPO_BANKGROUPTYPE" }]</label>
                                        <select name="dynvalue[fcpo_so_bankgrouptype_eps]">
                                            <option value="ARZ_OVB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_OVB"}]selected[{/if}]>Volksbanken</option>
                                            <option value="ARZ_BAF" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_BAF"}]selected[{/if}]>Bank f&uuml;r &Auml;rzte und Freie Berufe</option>
                                            <option value="ARZ_NLH" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_NLH"}]selected[{/if}]>Nieder&ouml;sterreichische Landes-Hypo</option>
                                            <option value="ARZ_VLH" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_VLH"}]selected[{/if}]>Vorarlberger Landes-Hypo</option>
                                            <option value="ARZ_BCS" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_BCS"}]selected[{/if}]>Bankhaus Carl Sp&auml;ngler & Co. AG</option>
                                            <option value="ARZ_HTB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_HTB"}]selected[{/if}]>Hypo Tirol</option>
                                            <option value="ARZ_HAA" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_HAA"}]selected[{/if}]>Hypo Alpe Adria</option>
                                            <option value="ARZ_IKB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_IKB"}]selected[{/if}]>Investkreditbank</option>
                                            <option value="ARZ_OAB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_OAB"}]selected[{/if}]>&Ouml;sterreichische Apothekerbank</option>
                                            <option value="ARZ_IMB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_IMB"}]selected[{/if}]>Immobank</option>
                                            <option value="ARZ_GRB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_GRB"}]selected[{/if}]>G&auml;rtnerbank</option>
                                            <option value="ARZ_HIB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "ARZ_HIB"}]selected[{/if}]>HYPO Investment</option>
                                            <option value="BA_AUS" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "BA_AUS"}]selected[{/if}]>Bank Austria</option>
                                            <option value="BAWAG_BWG" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "BAWAG_BWG"}]selected[{/if}]>BAWAG</option>
                                            <option value="BAWAG_PSK" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "BAWAG_PSK"}]selected[{/if}]>PSK Bank</option>
                                            <option value="BAWAG_ESY" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "BAWAG_ESY"}]selected[{/if}]>easybank</option>
                                            <option value="BAWAG_SPD" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "BAWAG_SPD"}]selected[{/if}]>Sparda Bank</option>
                                            <option value="SPARDAT_EBS" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "SPARDAT_EBS"}]selected[{/if}]>Erste Bank</option>
                                            <option value="SPARDAT_BBL" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "SPARDAT_BBL"}]selected[{/if}]>Bank Burgenland</option>
                                            <option value="RAC_RAC" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "RAC_RAC"}]selected[{/if}]>Raiffeisen</option>
                                            <option value="HRAC_OOS" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "HRAC_OOS"}]selected[{/if}]>Hypo Ober&ouml;sterreich</option>
                                            <option value="HRAC_SLB" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "HRAC_SLB"}]selected[{/if}]>Hypo Salzburg</option>
                                            <option value="HRAC_STM" [{ if $dynvalue.fcpo_so_bankgrouptype_eps == "HRAC_STM"}]selected[{/if}]>Hypo Steiermark</option>
                                        </select>
                                    </li>
                                    <li id="fcpo_ou_idl" style="display: none;">
                                        <label>[{ oxmultilang ident="FCPO_BANKGROUPTYPE" }]</label>
                                        <select name="dynvalue[fcpo_so_bankgrouptype_idl]">
                                            <option value="ABN_AMRO_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "ABN_AMRO_BANK"}]selected[{/if}]>ABN AMRO Bank</option>
                                            <option value="FORTIS_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "FORTIS_BANK"}]selected[{/if}]>Fortis Bank</option>
                                            <option value="FRIESLAND_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "FRIESLAND_BANK"}]selected[{/if}]>Friesland Bank</option>
                                            <option value="ING_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "ING_BANK"}]selected[{/if}]>ING Bank</option>
                                            <option value="RABOBANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "RABOBANK"}]selected[{/if}]>Rabobank</option>
                                            <option value="SNS_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "SNS_BANK"}]selected[{/if}]>SNS Bank</option>
                                            <option value="ASN_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "ASN_BANK"}]selected[{/if}]>ASN Bank</option>
                                            <option value="SNS_REGIO_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "SNS_REGIO_BANK"}]selected[{/if}]>SNS Regio Bank</option>
                                            <option value="TRIODOS_BANK" [{ if $dynvalue.fcpo_so_bankgrouptype_idl == "TRIODOS_BANK"}]selected[{/if}]>Triodos Bank</option>
                                        </select>
                                    </li>
                                </ul>
                                <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
                                [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                                    <div class="desc">
                                        [{ $paymentmethod->oxpayments__oxlongdesc->value}]
                                    </div>
                                [{/if}]
                            </dd>
                        </dl>
                    [{/if}]
                [{else}]
                    [{include file="page/checkout/inc/payment_other.tpl"}]
                [{/if}]
            [{/block}]
        [{/foreach}]

        [{* TRUSTED SHOPS BEGIN *}]
        [{include file="page/checkout/inc/trustedshops.tpl"}]
        [{* TRUSTED SHOPS END *}]

        [{block name="checkout_payment_nextstep"}]
        [{if $oView->isLowOrderPrice()}]
            <div class="lineBox clear">
            <div><b>[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_MINORDERPRICE" }] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</b></div>
            </div>
        [{else}]
            <div class="lineBox clear">
                <a href="[{ oxgetseourl ident=$oViewConf->getOrderLink() }]" class="prevStep submitButton largeButton" id="paymentBackStepBottom">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_BACKSTEP" }]</a>
                <button type="submit" name="userform" class="submitButton nextStep largeButton" id="paymentNextStepBottom">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_NEXTSTEP" }]</button>
            </div>
        [{/if}]
        [{/block}]

    [{elseif $oView->getEmptyPayment()}]
        [{block name="checkout_payment_nopaymentsfound"}]
        <div class="lineBlock"></div>
        <h3 id="paymentHeader" class="blockHead">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_INFO" }]</h3>
        [{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_EMPTY_TEXT" }]
        <input type="hidden" name="paymentid" value="oxempty">
        <div class="lineBox clear">
            <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=user" }]" class="prevStep submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_BACKSTEP" }]</a>
            <button type="submit" name="userform" class="submitButton nextStep largeButton" id="paymentNextStepBottom">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_NEXTSTEP" }]</button>
        </div>
        [{/block}]
    [{/if}]
</form>