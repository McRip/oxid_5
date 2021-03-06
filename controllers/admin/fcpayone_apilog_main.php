<?php

/**
 * Sets template, that arranges two other templates ("fcpayone_apilog_list.tpl" and "fcpayone_apilog.tpl") to frame.
 * 
 * @author FATCHIP GmbH | Robert M�ller
 */
class fcpayone_apilog_main extends oxAdminView {
    
    /**
     * Current class template name.
     * 
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_apilog_main.tpl';

    /**
     * Returns current view identifier
     *
     * @return string
     */
    public function getViewId() {
        return 'dyn_fcpayone';
    }

    /**
     * Return shop-version as integer
     *
     * @return integer
     */
    protected function _fcGetCurrentVersion() {
        $sVersion = oxConfig::getInstance()->getActiveShop()->oxshops__oxversion->value;
        $iVersion = (int)str_replace('.', '', $sVersion);
        while ($iVersion < 1000) {
            $iVersion = $iVersion*10;
        }
        return $iVersion;
    }
    
    /**
     * Return admin template seperator sign by shop-version
     *
     * @return string
     */
    public function fcGetAdminSeperator() {
        $iVersion = $this->_fcGetCurrentVersion();
        if($iVersion < 4300) {
            return '?';
        } else {
            return '&';
        }
    }
    
}