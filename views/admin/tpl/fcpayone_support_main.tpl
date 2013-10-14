[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<a id="fcPayoneLink" style="display: block; background-color: #888888; color: white;padding: 5px;width: 180px;" href="https://www.payone.de/shopplugins/oxid/support.html?integratorid=[{$sIntegratorId}]&integratorver=[{$edition}][{$version}]&integratorextver=[{$sPayOneVersion}]&mid=[{$sMerchantId}]" target="_blank">Support-Fenster &ouml;ffnen</a>

<script type="text/javascript">
    window.open(top.basefrm.edit.document.getElementById( "fcPayoneLink" ).href);
</script>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]