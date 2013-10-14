[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
    
    function handlePresaveOrderCheckbox(oCheckbox) {
        if(oCheckbox.checked) {
            document.getElementById('reduce_stock').style.display = "";
        } else {
            document.getElementById('reduce_stock').style.display = "none";
        }
    }
    
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="fcpayone_main">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="fcpayone_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">

    [{ oxmultilang ident="FCPO_MAIN_CONFIG_INFOTEXT" }]<br><br>

    <table border="0" width="98%">
        <tr>
            <td class="edittext" style="width: 200px;">
                [{ oxmultilang ident="FCPO_MODULE_VERSION" }]
            </td>
            <td class="edittext" style="width: 120px;" colspan="2">
                [{$sModuleVersion}]
            </td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td class="edittext" style="width: 200px;">
                [{ oxmultilang ident="FCPO_MERCHANT_ID" }]
            </td>
            <td class="edittext" style="width: 120px;">
                <input type=text class="txt" name=confstrs[sFCPOMerchantID] value="[{$confstrs.sFCPOMerchantID}]" [{ $readonly}]>
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_MERCHANTID" }]</td>
        </tr>

        <tr>
            <td class="edittext" >
                [{ oxmultilang ident="FCPO_PORTAL_ID" }]
            </td>
            <td class="edittext">
                <input type=text class="txt" name=confstrs[sFCPOPortalID] value="[{$confstrs.sFCPOPortalID}]" [{ $readonly}]>
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_PORTALID" }]</td>
        </tr>

        <tr>
            <td class="edittext" >
                [{ oxmultilang ident="FCPO_PORTAL_KEY" }]
            </td>
            <td class="edittext">
                <input type=text class="txt" name=confstrs[sFCPOPortalKey] value="[{$confstrs.sFCPOPortalKey}]" [{ $readonly}]>
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_PORTALKEY" }]</td>
        </tr>

        <tr>
            <td class="edittext" >
                [{ oxmultilang ident="FCPO_SUBACCOUNT_ID" }]
            </td>
            <td class="edittext">
                <input type=text class="txt" name=confstrs[sFCPOSubAccountID] value="[{$confstrs.sFCPOSubAccountID}]" [{ $readonly}]>
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_SUBACCOUNTID" }]</td>
        </tr>

        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_SEND_ARTICLELIST"}]</td>
            <td class="edittext">
                <input type="hidden" name="confbools[blFCPOSendArticlelist]" value="false">
                <input type="checkbox" name="confbools[blFCPOSendArticlelist]" value="true" [{if ($confbools.blFCPOSendArticlelist)}]checked[{/if}]>
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_SEND_ARTICLELIST" }]</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_PRESAVE_ORDER"}]</td>
            <td class="edittext">
                <input type="hidden" name="confbools[blFCPOPresaveOrder]" value="false">
                <input type="checkbox" name="confbools[blFCPOPresaveOrder]" value="true" [{if ($confbools.blFCPOPresaveOrder)}]checked[{/if}] onclick="handlePresaveOrderCheckbox(this);">
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_PRESAVE_ORDER" }]</td>
        </tr>
        <tr id="reduce_stock" [{if !($confbools.blFCPOPresaveOrder)}]style="display: none;"[{/if}]>
            <td class="edittext">[{ oxmultilang ident="FCPO_REDUCE_STOCK"}]</td>
            <td class="edittext">
                <input type="radio" name="confbools[blFCPOReduceStock]" value="0" [{if $confbools.blFCPOReduceStock == '0' || !$confbools.blFCPOReduceStock}]checked[{/if}]> [{oxmultilang ident="FCPO_REDUCE_STOCK_BEFORE"}]<br>
                <input type="radio" name="confbools[blFCPOReduceStock]" value="1" [{if $confbools.blFCPOReduceStock == '1'}]checked[{/if}]> [{oxmultilang ident="FCPO_REDUCE_STOCK_AFTER"}]
            </td>
            <td>[{ oxinputhelp ident="FCPO_HELP_REDUCE_STOCK" }]</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="edittext" colspan="3">
                <strong>[{ oxmultilang ident="FCPO_ACTIVE_CREDITCARD_TYPES"}]</strong><br>
                [{ oxmultilang ident="FCPO_CREDITCARDBRANDS_INFOTEXT" }]
            </td>
        </tr>

        <tr>
            <td class="edittext">Visa</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOVisaActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOVisaActivated]" value="true"  [{if ($confbools.blFCPOVisaActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=V&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCVLive]" value="1" [{if $confbools.blFCPOCCVLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCVLive]" value="0" [{if $confbools.blFCPOCCVLive == '0' || !$confbools.blFCPOCCVLive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Mastercard</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOMastercardActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOMastercardActivated]" value="true"  [{if ($confbools.blFCPOMastercardActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=M&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCMLive]" value="1" [{if $confbools.blFCPOCCMLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCMLive]" value="0" [{if $confbools.blFCPOCCMLive == '0' || !$confbools.blFCPOCCMLive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Amex</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOAmexActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOAmexActivated]" value="true"  [{if ($confbools.blFCPOAmexActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=A&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCALive]" value="1" [{if $confbools.blFCPOCCALive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCALive]" value="0" [{if $confbools.blFCPOCCALive == '0' || !$confbools.blFCPOCCALive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Diners</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPODinersActivated]" value="false">
                <input type=checkbox name="confbools[blFCPODinersActivated]" value="true"  [{if ($confbools.blFCPODinersActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=D&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCDLive]" value="1" [{if $confbools.blFCPOCCDLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCDLive]" value="0" [{if $confbools.blFCPOCCDLive == '0' || !$confbools.blFCPOCCDLive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">JCB</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOJCBActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOJCBActivated]" value="true"  [{if ($confbools.blFCPOJCBActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=J&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCJLive]" value="1" [{if $confbools.blFCPOCCJLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCJLive]" value="0" [{if $confbools.blFCPOCCJLive == '0' || !$confbools.blFCPOCCJLive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Maestro International</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOMaestroIntActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOMaestroIntActivated]" value="true"  [{if ($confbools.blFCPOMaestroIntActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=O&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCOLive]" value="1" [{if $confbools.blFCPOCCOLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCOLive]" value="0" [{if $confbools.blFCPOCCOLive == '0' || !$confbools.blFCPOCCOLive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Maestro UK</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOMaestroUKActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOMaestroUKActivated]" value="true"  [{if ($confbools.blFCPOMaestroUKActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=U&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCULive]" value="1" [{if $confbools.blFCPOCCULive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCULive]" value="0" [{if $confbools.blFCPOCCULive == '0' || !$confbools.blFCPOCCULive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Discover</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPODiscoverActivated]" value="false">
                <input type=checkbox name="confbools[blFCPODiscoverActivated]" value="true"  [{if ($confbools.blFCPODiscoverActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=C&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCCLive]" value="1" [{if $confbools.blFCPOCCCLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCCLive]" value="0" [{if $confbools.blFCPOCCCLive == '0' || !$confbools.blFCPOCCCLive}]checked[{/if}]> Test
            </td>
        </tr>


        <tr>
            <td class="edittext">Carte Bleue</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOCarteBleueActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOCarteBleueActivated]" value="true"  [{if ($confbools.blFCPOCarteBleueActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=B&amp;type=cc');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOCCBLive]" value="1" [{if $confbools.blFCPOCCBLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOCCBLive]" value="0" [{if $confbools.blFCPOCCBLive == '0' || !$confbools.blFCPOCCBLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="edittext" colspan="3">
                <strong>[{ oxmultilang ident="FCPO_ACTIVE_ONLINE_UBERWEISUNG_TYPES"}]</strong><br>
                [{ oxmultilang ident="FCPO_ONLINEUBERWEISUNG_INFOTEXT" }]
            </td>
        </tr>

        <tr>
            <td class="edittext">Sofort-&Uuml;berweisung</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOSofoActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOSofoActivated]" value="true"  [{if ($confbools.blFCPOSofoActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=PNT&amp;type=sb');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOSBPNTLive]" value="1" [{if $confbools.blFCPOSBPNTLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOSBPNTLive]" value="0" [{if $confbools.blFCPOSBPNTLive == '0' || !$confbools.blFCPOSBPNTLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr>
            <td class="edittext">giropay</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOgiroActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOgiroActivated]" value="true"  [{if ($confbools.blFCPOgiroActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=GPY&amp;type=sb');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOSBGPYLive]" value="1" [{if $confbools.blFCPOSBGPYLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOSBGPYLive]" value="0" [{if $confbools.blFCPOSBGPYLive == '0' || !$confbools.blFCPOSBGPYLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr>
            <td class="edittext">eps - Online-&Uuml;berweisung</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOepsActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOepsActivated]" value="true"  [{if ($confbools.blFCPOepsActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=EPS&amp;type=sb');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOSBEPSLive]" value="1" [{if $confbools.blFCPOSBEPSLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOSBEPSLive]" value="0" [{if $confbools.blFCPOSBEPSLive == '0' || !$confbools.blFCPOSBEPSLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr>
            <td class="edittext">PostFinance E-Finance</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOPoFiEFActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOPoFiEFActivated]" value="true"  [{if ($confbools.blFCPOPoFiEFActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=PFF&amp;type=sb');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOSBPFFLive]" value="1" [{if $confbools.blFCPOSBPFFLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOSBPFFLive]" value="0" [{if $confbools.blFCPOSBPFFLive == '0' || !$confbools.blFCPOSBPFFLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr>
            <td class="edittext">PostFinance Card</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOPoFiCaActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOPoFiCaActivated]" value="true"  [{if ($confbools.blFCPOPoFiCaActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=PFC&amp;type=sb');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOSBPFCLive]" value="1" [{if $confbools.blFCPOSBPFCLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOSBPFCLive]" value="0" [{if $confbools.blFCPOSBPFCLive == '0' || !$confbools.blFCPOSBPFCLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr>
            <td class="edittext">iDeal</td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOiDealActivated]" value="false">
                <input type=checkbox name="confbools[blFCPOiDealActivated]" value="true"  [{if ($confbools.blFCPOiDealActivated)}]checked[{/if}]>
                <input type="button" onclick="JavaScript:showDialog('&amp;cl=fcpayone_main&amp;aoc=1&amp;oxid=IDL&amp;type=sb');" class="" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]">
                [{ oxinputhelp ident="FCPO_HELP_ASSIGNCOUNTRIES" }]
                <input type="radio" name="confbools[blFCPOSBIDLLive]" value="1" [{if $confbools.blFCPOSBIDLLive == '1'}]checked[{/if}]> <strong>Live</strong>
                <input type="radio" name="confbools[blFCPOSBIDLLive]" value="0" [{if $confbools.blFCPOSBIDLLive == '0' || !$confbools.blFCPOSBIDLLive}]checked[{/if}]> Test
            </td>
        </tr>

        <tr>
            <td class="edittext"></td>
            <td class="edittext"><br>
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" [{ $readonly}]>
            </td>
            <td></td>
        </tr>

    </table>
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]