<!-- FCPAYONE BEGIN -->
[{if $edit->oxpayments__fcpoispayone->value == 1}]
    <tr>
        <td class="edittext" colspan="2">
            <img src="[{$oViewConf->getImageUrl()}]logoclaim.gif" alt="PAYONE"><br><br>
            [{ oxmultilang ident="FC_IS_PAYONE" }]
			[{if $edit->oxpayments__oxid->value == 'fcpocommerzfinanz'}]
				<input type="hidden" name="editval[oxpayments__fcpoauthmode]" value="preauthorization">
			[{/if}]
        </td>
    </tr>
    <tr>
    [{if $edit->oxpayments__oxid->value != 'fcpocommerzfinanz'}]
        <td class="edittext" width="70">
            [{ oxmultilang ident="FCPO_AUTHORIZATION_METHOD" }]
        </td>
        <td class="edittext">
            <input type="radio" name="editval[oxpayments__fcpoauthmode]" value="preauthorization" [{if $edit->oxpayments__fcpoauthmode->value == 'preauthorization'}]checked[{/if}]> [{ oxmultilang ident="FCPO_PREAUTHORIZATION" }] [{ oxinputhelp ident="FCPO_PREAUTHORIZATION_HELP" }]<br>
            <input type="radio" name="editval[oxpayments__fcpoauthmode]" value="authorization" [{if $edit->oxpayments__fcpoauthmode->value == 'authorization'}]checked[{/if}]> [{ oxmultilang ident="FCPO_AUTHORIZATION" }] [{ oxinputhelp ident="FCPO_AUTHORIZATION_HELP" }]
        </td>
    [{/if}]
    </tr>
    <tr>
        <td class="edittext" width="70">
            [{ oxmultilang ident="FCPO_OPERATION_MODE" }]
        </td>
        <td class="edittext">
            [{if $edit->getId() == 'fcpocreditcard' || $edit->getId() == 'fcpoonlineueberweisung'}]
                [{ oxmultilang ident="FCPO_INFOTEXT_SET_OPERATIONMODE" }]
            [{else}]
                <table>
                    <tr>
                        <td>
                            <input type="radio" name="editval[oxpayments__fcpolivemode]" value="1" [{if $edit->oxpayments__fcpolivemode->value == '1'}]checked[{/if}]> <strong>[{ oxmultilang ident="FCPO_LIVE_MODE" }]</strong><br>
                            <input type="radio" name="editval[oxpayments__fcpolivemode]" value="0" [{if $edit->oxpayments__fcpolivemode->value == '0'}]checked[{/if}]> [{ oxmultilang ident="FCPO_TEST_MODE" }]<br>
                        </td>
                        <td>
                            [{ oxinputhelp ident="FCPO_HELP_OPERATIONMODE" }]
                        </td>
                    </tr>
                </table>
            [{/if}]
        </td>
    </tr>
[{else}]
    <tr>
        <td colspan="2">
            <input type="hidden" name="editval[oxpayments__fcpoauthmode]" value="">
        </td>
    </tr>
[{/if}]
<!-- FCPAYONE END -->
<tr>
    <td class="edittext" width="70">
    [{ oxmultilang ident="GENERAL_ACTIVE" }]
    </td>
    <td class="edittext">
    <input class="edittext" type="checkbox" name="editval[oxpayments__oxactive]" value='1' [{if $edit->oxpayments__oxactive->value == 1}]checked[{/if}] [{ $readonly }]>
    [{ oxinputhelp ident="HELP_GENERAL_ACTIVE" }]
    </td>
</tr>
<tr>
    <td class="edittext" width="100">
    [{ oxmultilang ident="PAYMENT_MAIN_NAME" }]
    </td>
    <td class="edittext">
    <input type="text" class="editinput" size="25" maxlength="[{$edit->oxpayments__oxdesc->fldmax_length}]" name="editval[oxpayments__oxdesc]" value="[{$edit->oxpayments__oxdesc->value}]" [{ $readonly }]>
    [{ oxinputhelp ident="HELP_PAYMENT_MAIN_NAME" }]
    </td>
</tr>
<tr>
    <td class="edittext">
    [{ oxmultilang ident="PAYMENT_MAIN_ADDPRICE" }] ([{ $oActCur->sign }])
    </td>
    <td class="edittext">
    <input type="text" class="editinput" size="15" maxlength="[{$edit->oxpayments__oxaddsum->fldmax_length}]" name="editval[oxpayments__oxaddsum]" value="[{$edit->oxpayments__oxaddsum->value }]" [{ $readonly }]>
        <select name="editval[oxpayments__oxaddsumtype]" class="editinput" [{include file="help.tpl" helpid=addsumtype}] [{ $readonly }]>
        [{foreach from=$sumtype item=sum}]
        <option value="[{ $sum }]" [{ if $sum == $edit->oxpayments__oxaddsumtype->value}]SELECTED[{/if}]>[{ $sum }]</option>
        [{/foreach}]
        </select>
    [{ oxinputhelp ident="HELP_PAYMENT_MAIN_ADDPRICE" }]
    </td>
</tr>
<tr>
    <td class="edittext" valign="top">
    [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES"}]
    </td>
    <td class="edittext">
      <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="1" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 1}]checked[{/if}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_ALLGOODS"}]</td>
            <td rowspan="5" valign="top">[{oxinputhelp ident="HELP_PAYMENT_MAIN_ADDSUMRULES"}]</td>
        </tr>
        <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="2" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 2}]checked[{/if}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_DISCOUNTS"}]</td></tr>
        <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="4" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 4}]checked[{/if}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_VOUCHERS"}]</td></tr>
        <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="8" [{if !$edit->oxpayments__oxaddsumrules->value || $edit->oxpayments__oxaddsumrules->value & 8}]checked[{/if}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_SHIPCOSTS"}]</td></tr>
        <tr><td><input type="checkbox" name="oxpayments__oxaddsumrules[]" value="16" [{if $edit->oxpayments__oxaddsumrules->value & 16}]checked[{/if}]> [{oxmultilang ident="PAYMENT_MAIN_ADDSUMRULES_GIFTS"}]</td></tr>
      </table>
    </td>
</tr>
<tr>
    <td class="edittext">
    [{ oxmultilang ident="PAYMENT_MAIN_FROMBONI" }]
    </td>
    <td class="edittext">
    <input type="text" class="editinput" size="25" maxlength="[{$edit->oxpayments__oxfromboni->fldmax_length}]" name="editval[oxpayments__oxfromboni]" value="[{$edit->oxpayments__oxfromboni->value}]" [{ $readonly }]>
    [{ oxinputhelp ident="HELP_PAYMENT_MAIN_FROMBONI" }]
    </td>
</tr>
<tr>
    <td class="edittext">
    [{ oxmultilang ident="PAYMENT_MAIN_AMOUNT" }] ([{ $oActCur->sign }])
    </td>
    <td class="edittext">
    [{ oxmultilang ident="PAYMENT_MAIN_FROM" }] <input type="text" class="editinput" size="5" maxlength="[{$edit->oxpayments__oxfromamount->fldmax_length}]" name="editval[oxpayments__oxfromamount]" value="[{$edit->oxpayments__oxfromamount->value}]" [{ $readonly }]>  [{ oxmultilang ident="PAYMENT_MAIN_TILL" }] <input type="text" class="editinput" size="5" maxlength="[{$edit->oxpayments__oxtoamount->fldmax_length}]" name="editval[oxpayments__oxtoamount]" value="[{$edit->oxpayments__oxtoamount->value}]" [{ $readonly }]>
    [{ oxinputhelp ident="HELP_PAYMENT_MAIN_AMOUNT" }]
    </td>
</tr>

<tr>
    <td class="edittext">
    [{ oxmultilang ident="PAYMENT_MAIN_SELECTED" }]
    </td>
    <td class="edittext">
    <input type="checkbox" name="editval[oxpayments__oxchecked]" value="1" [{if $edit->oxpayments__oxchecked->value}]checked[{/if}] [{ $readonly }]>
    [{ oxinputhelp ident="HELP_PAYMENT_MAIN_SELECTED" }]
    </td>
</tr>
<tr>
    <td class="edittext">
    [{ oxmultilang ident="GENERAL_SORT" }]
    </td>
    <td class="edittext">
    <input type="text" class="editinput" size="25" maxlength="[{$edit->oxpayments__oxsort->fldmax_length}]" name="editval[oxpayments__oxsort]" value="[{$edit->oxpayments__oxsort->value}]" [{ $readonly }]>
    [{ oxinputhelp ident="HELP_PAYMENT_MAIN_SORT" }]
    </td>
</tr>