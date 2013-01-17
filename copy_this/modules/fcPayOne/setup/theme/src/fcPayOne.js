function getSelectedPaymentMethod() {
    var oForm = getPaymentForm();
    if(oForm && oForm.paymentid) {
        if(oForm.paymentid.length) {
            for(var i = 0;i < oForm.paymentid.length; i++) {
                if(oForm.paymentid[i].checked == true) {
                    return oForm.paymentid[i].value;
                }
            }
        } else {
            return oForm.paymentid.value;
        }
    }
    return false;
}

function getPaymentForm() {
    if(document.order) {
        if(document.order[0].nodeName != 'FORM' && document.order.paymentid) {
            return document.order;
        } else {
            for(var i = 0; i < document.order.length; i++) {
                if(document.order[i].paymentid) {
                    return document.order[i];
                }
            }
        }
    }
    return false;
}

function getOperationMode(sType) {
    var sSelectedPaymentOperationMode = 'fcpo_mode_' + getSelectedPaymentMethod();
    if(sType != '') {
        sSelectedPaymentOperationMode += '_' + sType;
    }
    var oForm = getPaymentForm();
    return oForm[sSelectedPaymentOperationMode].value;
}

function fcCheckType(select) {
    if(select.options[select.selectedIndex].value == 'U') {
        document.getElementById('fcpo_kkcsn_row').style.display = 'table-row';
    } else {
        document.getElementById('fcpo_kkcsn_row').style.display = 'none';
    }
}

function fcCheckOUType(select) {
    if(document.getElementById('fcpo_ou_blz')) {
        document.getElementById('fcpo_ou_blz').style.display = 'none';
    }
    if(document.getElementById('fcpo_ou_ktonr')) {
        document.getElementById('fcpo_ou_ktonr').style.display = 'none';
    }
    if(document.getElementById('fcpo_ou_eps')) {
        document.getElementById('fcpo_ou_eps').style.display = 'none';
    }
    if(document.getElementById('fcpo_ou_idl')){
        document.getElementById('fcpo_ou_idl').style.display = 'none';
    }
    if(select.options[select.selectedIndex].value == 'PNT') {
        document.getElementById('fcpo_ou_blz').style.display = '';
        document.getElementById('fcpo_ou_ktonr').style.display = '';
    }

    if(select.options[select.selectedIndex].value == 'GPY') {
        document.getElementById('fcpo_ou_blz').style.display = '';
        document.getElementById('fcpo_ou_ktonr').style.display = '';
    }

    if(select.options[select.selectedIndex].value == 'EPS') {
        document.getElementById('fcpo_ou_eps').style.display = '';
    }

    if(select.options[select.selectedIndex].value == 'IDL') {
        document.getElementById('fcpo_ou_idl').style.display = '';
    }
}
var oForm = getPaymentForm();
if(oForm["dynvalue[fcpo_sotype]"]) {
    fcCheckOUType(oForm["dynvalue[fcpo_sotype]"]);
}

function resetErrorContainers() {
    if(document.getElementById('fcpo_cc_number_invalid')) {
        document.getElementById('fcpo_cc_number_invalid').style.display = '';
    }
    if(document.getElementById('fcpo_cc_date_invalid')) {
        document.getElementById('fcpo_cc_date_invalid').style.display = '';
    }
    if(document.getElementById('fcpo_cc_cvc2_invalid')) {
        document.getElementById('fcpo_cc_cvc2_invalid').style.display = '';
    }
    if(document.getElementById('fcpo_cc_error')) {
        document.getElementById('fcpo_cc_error').style.display = '';
    }
    if(document.getElementById('fcpo_cc_error_content')) {
        document.getElementById('fcpo_cc_error_content').innerHTML = '';
    }
    if(document.getElementById('fcpo_elv_blz_invalid')) {
        document.getElementById('fcpo_elv_blz_invalid').style.display = '';
    }
    if(document.getElementById('fcpo_elv_ktonr_invalid')) {
        document.getElementById('fcpo_elv_ktonr_invalid').style.display = '';
    }
    if(document.getElementById('fcpo_elv_error')) {
        document.getElementById('fcpo_elv_error').style.display = '';
    }
    if(document.getElementById('fcpo_elv_error_content')) {
        document.getElementById('fcpo_elv_error_content').innerHTML = '';
    }
    if(document.getElementById('fcpo_ou_blz_invalid')) {
        document.getElementById('fcpo_ou_blz_invalid').style.display = '';
    }
    if(document.getElementById('fcpo_ou_ktonr_invalid')) {
        document.getElementById('fcpo_ou_ktonr_invalid').style.display = '';
    }
    
    if(document.getElementById('fcpo_ou_error')) {
        document.getElementById('fcpo_ou_error').style.display = '';
    }
    if(document.getElementById('fcpo_ou_error_content')) {
        document.getElementById('fcpo_ou_error_content').innerHTML = '';
    }
}

function startCCRequest() {
    resetErrorContainers();
    var oForm = getPaymentForm();
    if(oForm["dynvalue[fcpo_kknumber]"].value == '') {
        document.getElementById('fcpo_cc_number_invalid').style.display = 'block';
        return false;
    }

    if(oForm["dynvalue[fcpo_kkpruef]"].value == '' || oForm["dynvalue[fcpo_kkpruef]"].value.length < 3) {
        document.getElementById('fcpo_cc_cvc2_invalid').style.display = 'block';
        return false;
    }

    var sKKType = oForm["dynvalue[fcpo_kktype]"].options[oForm["dynvalue[fcpo_kktype]"].selectedIndex].value;

    var sMode = getOperationMode(sKKType);

    var data = {
        mid : oForm.fcpo_mid.value,
        portalid : oForm.fcpo_portalid.value,
        mode : sMode,
        request : 'creditcardcheck',
        responsetype : 'JSON',
        hash : oForm["fcpo_hashcc_" + sKKType].value,
        encoding : oForm.fcpo_encoding.value,
        aid : oForm.fcpo_aid.value,
        cardpan : oForm["dynvalue[fcpo_kknumber]"].value,
        cardtype : sKKType,
        cardexpiredate : oForm["dynvalue[fcpo_kkyear]"].options[oForm["dynvalue[fcpo_kkyear]"].selectedIndex].innerHTML.substr(2,2) + oForm["dynvalue[fcpo_kkmonth]"].options[oForm["dynvalue[fcpo_kkmonth]"].selectedIndex].innerHTML,
        cardcvc2 : oForm["dynvalue[fcpo_kkpruef]"].value,
        storecarddata : 'yes',
        language : oForm.fcpo_tpllang.value,
        integrator_name : 'oxid',
        integrator_version : oForm.fcpo_integratorver.value,
        solution_name : 'fatchip',
        solution_version : oForm.fcpo_integratorextver.value
        
    };
    if(sKKType == 'U') {
        data.cardsequencenumber = oForm["dynvalue[fcpo_kkcsn]"].value;
    }
    var options = {
        return_type : 'object',
        callback_function_name : 'processPayoneResponseCC'
    };

    var request = new PayoneRequest(data, options);
    request.checkAndStore();
    return false;
}

function getCleanedNumber(dirtyNumber) {
    var cleanedNumber = '';
    var tmpChar;
    for (i = 0; i < dirtyNumber.length; i++) {
        tmpChar = dirtyNumber.charAt(i);
        if (tmpChar != ' ' && !isNaN(tmpChar)) {
            cleanedNumber = cleanedNumber + tmpChar;
        }
    }
    return cleanedNumber;
}

function checkOnlineUeberweisung() {
    resetErrorContainers();
    var oForm = getPaymentForm();
    if(oForm['dynvalue[fcpo_sotype]'].value == 'PNT' || oForm['dynvalue[fcpo_sotype]'].value == 'GPY') {
        oForm['dynvalue[fcpo_ou_blz]'].value = getCleanedNumber(oForm['dynvalue[fcpo_ou_blz]'].value);
        if(oForm['dynvalue[fcpo_ou_blz]'].value == '' || (oForm.fcpo_bill_country.value == 'DE' && oForm['dynvalue[fcpo_ou_blz]'].value.length != 8)) {
            document.getElementById('fcpo_ou_blz_invalid').style.display = 'block';
            return false;
        }

        oForm['dynvalue[fcpo_ou_ktonr]'].value = getCleanedNumber(oForm['dynvalue[fcpo_ou_ktonr]'].value);
        if(oForm['dynvalue[fcpo_ou_ktonr]'].value == '') {
            document.getElementById('fcpo_ou_ktonr_invalid').style.display = 'block';
            return false;
        }
        
        if(oForm['dynvalue[fcpo_sotype]'].value == 'GPY' && oForm.fcpo_bill_country.value != 'DE') {
            document.getElementById('fcpo_ou_error_content').innerHTML = 'Zahlart ist nur in Deutschland verf&uuml;gbar.';
            document.getElementById('fcpo_ou_error').style.display = 'block';
            return false;
        }
        
        if(oForm['dynvalue[fcpo_sotype]'].value == 'PNT' && oForm.fcpo_bill_country.value != 'DE' && oForm.fcpo_bill_country.value != 'AT' && oForm.fcpo_bill_country.value != 'CH') {
            document.getElementById('fcpo_ou_error_content').innerHTML = 'Zahlart ist nur in Deutschland, &Ouml;sterreich und der Schweiz verf&uuml;gbar.';
            document.getElementById('fcpo_ou_error').style.display = 'block';
            return false;
        }
    }
    return true;
}

function startELVRequest() {
    resetErrorContainers();
    var oForm = getPaymentForm();

    oForm['dynvalue[fcpo_elv_blz]'].value = getCleanedNumber(oForm['dynvalue[fcpo_elv_blz]'].value);
    if(oForm['dynvalue[fcpo_elv_blz]'].value == '' || (oForm.fcpo_bill_country.value == 'DE' && oForm['dynvalue[fcpo_elv_blz]'].value.length != 8)) {
        document.getElementById('fcpo_elv_blz_invalid').style.display = 'block';
        return false;
    }

    oForm['dynvalue[fcpo_elv_ktonr]'].value = getCleanedNumber(oForm['dynvalue[fcpo_elv_ktonr]'].value);
    if(oForm['dynvalue[fcpo_elv_ktonr]'].value == '') {
        document.getElementById('fcpo_elv_ktonr_invalid').style.display = 'block';
        return false;
    }
    
    if(oForm.fcpo_bill_country.value != 'DE' && oForm.fcpo_bill_country.value != 'AT') {
        document.getElementById('fcpo_elv_error_content').innerHTML = 'Zahlart ist nur in Deutschland und &Ouml;sterreich verf&uuml;gbar.';
        document.getElementById('fcpo_elv_error').style.display = 'block';
        return false;
    }
    
    if(oForm.fcpo_checktype && oForm.fcpo_checktype.value == '-1') {
        oForm.submit();
        return false;
    }        

    var sMode = getOperationMode('');
    var data = {
        mid : oForm.fcpo_mid.value,
        portalid : oForm.fcpo_portalid.value,
        mode : sMode,
        request : 'bankaccountcheck',
        responsetype : 'JSON',
        hash : oForm.fcpo_hashelvWith.value,
        encoding : oForm.fcpo_encoding.value,
        aid : oForm.fcpo_aid.value,
        checktype : oForm.fcpo_checktype.value,
        bankaccount : oForm['dynvalue[fcpo_elv_ktonr]'].value,
        bankcode : oForm['dynvalue[fcpo_elv_blz]'].value,
        bankcountry : oForm.fcpo_bill_country.value,
        language : oForm.fcpo_tpllang.value,
        integrator_name : 'oxid',
        integrator_version : oForm.fcpo_integratorver.value,
        solution_name : 'fatchip',
        solution_version : oForm.fcpo_integratorextver.value
    };
    var options = {
        return_type : 'object',
        callback_function_name : 'processPayoneResponseELV'
    };

    var request = new PayoneRequest(data, options);
    request.checkAndStore();

    return false;
}

function fcCheckPaymentSelection() {
    var sCheckedValue = getSelectedPaymentMethod();
    if(sCheckedValue != false) {
        if(sCheckedValue == 'fcpocreditcard') {
            return startCCRequest();
        } else if(sCheckedValue == 'fcpodebitnote') {
            return startELVRequest(true);
        } else if(sCheckedValue == 'fcpoonlineueberweisung') {
            return checkOnlineUeberweisung();
        }
    }
    return true;
}

function processPayoneResponseELV(response) {
    if(response.get('status') != 'VALID') {
        if(response.get('errorcode') == '1083') {
            document.getElementById('fcpo_elv_ktonr_invalid').style.display = 'block';
        } else if(response.get('errorcode') == '1084' || response.get('errorcode') == '884') {
            document.getElementById('fcpo_elv_blz_invalid').style.display = 'block';
        } else {
            document.getElementById('fcpo_elv_error_content').innerHTML = '"'+response.get('customermessage')+'"';
            document.getElementById('fcpo_elv_error').style.display = 'block';
        }
    } else {
        var oForm = getPaymentForm();
        oForm.submit();
    }
}

function processPayoneResponseCC(response) {
    if(response.get('status') == 'VALID') {
        var oForm = getPaymentForm();
        oForm["dynvalue[fcpo_pseudocardpan]"].value = response.get('pseudocardpan');
        oForm["dynvalue[fcpo_ccmode]"].value = getOperationMode(oForm["dynvalue[fcpo_kktype]"].options[oForm["dynvalue[fcpo_kktype]"].selectedIndex].value);
        oForm["dynvalue[fcpo_kknumber]"].value = response.get('truncatedcardpan');
        oForm["dynvalue[fcpo_kkpruef]"].value = 'xxx';
        oForm.submit();
    } else if(response.get('status') != 'VALID') {
        if(response.get('errorcode') == '1078' || response.get('errorcode') == '877') {
            document.getElementById('fcpo_cc_number_invalid').style.display = 'block';
        } else if(response.get('errorcode') == '1079') {
            document.getElementById('fcpo_cc_cvc2_invalid').style.display = 'block';
        } else if(response.get('errorcode') == '33') {
            document.getElementById('fcpo_cc_date_invalid').style.display = 'block';
        } else {
            document.getElementById('fcpo_cc_error_content').innerHTML = '"'+response.get('customermessage')+'"';
            document.getElementById('fcpo_cc_error').style.display = 'block';
        }
    }
}