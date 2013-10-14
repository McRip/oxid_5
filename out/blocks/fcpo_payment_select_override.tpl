[{if $sPaymentID == "fcpocreditcard"}]
	[{if $oView->hasPaymentMethodAvailableSubTypes('cc') }]
		[{ assign var="dynvalue" value=$oView->getDynValue()}]
		<dl>
			<dt>
				<input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
				<label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum != 0 }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
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
							<input type="hidden" nam    e="fcpo_hashcc_A" value="[{$oView->getHashCC('A')}]">
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
				[{if $paymentmethod->oxpayments__oxlongdesc->value}]
					<div class="desc">
						[{ $paymentmethod->oxpayments__oxlongdesc->rawValue}]
					</div>
				[{/if}]
			</dd>
		</dl>
		<input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
	[{/if}]
[{elseif $sPaymentID == "fcpodebitnote"}]
	[{ assign var="dynvalue" value=$oView->getDynValue()}]
	<dl>
		<dt>
			<input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
			<label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum != 0 }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
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
				<label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum != 0 }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
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
						[{ $paymentmethod->oxpayments__oxlongdesc->rawValue}]
					</div>
				[{/if}]
			</dd>
		</dl>
	[{/if}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]