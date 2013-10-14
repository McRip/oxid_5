<?php

/**
 * This admin view show a PAYONE website in a frame with common informations
 * about the module
 *
 * @author FATCHIP GmbH | Robert M�ller
 */
class fcpayone_support_main extends oxAdminView {
    
    /**
     * Current class template name.
     * 
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_support_main.tpl';

    /**
     * Loads shop version, PAYONE module version und PAYONE merchant ID and passes them to Smarty engine, returns
     * name of template file "fcpayone_support_main.tpl".
     *
     * @return string
     */
    public function render() {
        $sReturn = parent::render();
        $oConfig = $this->getConfig();
        $this->_aViewData["sPayOneVersion"] = fcpoRequest::getVersion();
        $this->_aViewData["sMerchantId"] = $oConfig->getConfigParam('sFCPOMerchantID');
        $this->_aViewData["sIntegratorId"] = $this->getIntegratorId();
        return $sReturn;
    }

    /**
     * Loads shop version and formats it in a certain way
     *
     * @return string
     */
    protected function getIntegratorId() {
        $sEdition = $this->getShopEdition();
        if($sEdition == 'CE') {
            return '2027000';
        } elseif($sEdition == 'PE') {
            return '2028000';
        } elseif($sEdition == 'EE') {
            return '2029000';
        }
        return '';
    }

    /**
     * Returns current view identifier
     *
     * @return string
     */
    public function getViewId() {
        return 'dyn_fcpayone';
    }

}