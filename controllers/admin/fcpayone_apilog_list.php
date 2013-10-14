<?php

/**
 * Admin API transaction log list.
 * Performs collection and managing (such as filtering or deleting) function.
 * 
 * @author FATCHIP GmbH | Robert Müller
 */
class fcpayone_apilog_list extends oxAdminList {

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'fcporequestlog';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSort = "fcporequestlog.fcpo_timestamp desc";

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_apilog_list.tpl';

    /**
     * Get config parameter PAYONE portal ID
     *
     * @return $string
     */
    public function getPortalId() {
        $oConfig = oxConfig::getInstance();
        return $oConfig->getConfigParam('sFCPOPortalID');
    }

    /**
     * Get config parameter PAYONE sub-account ID
     *
     * @return $string
     */
    public function getSubAccountId() {
        $oConfig = oxConfig::getInstance();
        return $oConfig->getConfigParam('sFCPOSubAccountID');
    }

    /**
     * Filter log entries, show only log entries of configured PAYONE account
     *
     * @param array  $aWhere SQL condition array
     * @param string $sQ     SQL query string
     *
     * @return string
     */
    protected function _prepareWhereQuery( $aWhere, $sQ ) {
        $sQ = parent::_prepareWhereQuery( $aWhere, $sQ );
        $sPortalId = $this->getPortalId();
        $sAid = $this->getSubAccountId();
        return $sQ." AND fcporequestlog.fcpo_portalid = '{$sPortalId}' AND fcporequestlog.fcpo_aid = '{$sAid}' ";
    }
    
    /**
     * Returns list filter array
     *
     * @return array
     */
    public function getListFilter()
    {
        if ( $this->_aListFilter === null ) {
            $this->_aListFilter = oxConfig::getParameter( "where" );
        }

        return $this->_aListFilter;
    }
    
    /**
     * Returns sorting fields array
     *
     * @return array
     */
    public function getListSorting()
    {
        if ( $this->_aCurrSorting === null ) {
            $this->_aCurrSorting = oxConfig::getParameter( 'sort' );

            if ( !$this->_aCurrSorting && $this->_sDefSortField && ( $oBaseObject = $this->getItemListBaseObject() ) ) {
                $this->_aCurrSorting[$oBaseObject->getCoreTableName()] = array( $this->_sDefSortField => "asc" );
            }
        }

        return $this->_aCurrSorting;
    }
    
    /**
     * Return input name for searchfields in list by shop-version
     *
     * @return string
     */
    public function fcGetInputName($sTable, $sField) {
        if($this->getCurrentVersion() >= 4500) {
            return "where[{$sTable}][{$sField}]";
        }
        return "where[{$sTable}.{$sField}]";
    }

    /**
     * Return shop-version as integer
     *
     * @return integer
     */
    public function getCurrentVersion() {
        return $this->_versionToInt(oxConfig::getInstance()->getActiveShop()->oxshops__oxversion->value);
    }

    /**
     * Format shop-version as integer
     *
     * @return integer
     */
    protected function _versionToInt($sVersion) {
        $iVersion = (int)str_replace('.', '', $sVersion);
        while ($iVersion < 1000) {
            $iVersion = $iVersion*10;
        }
        return $iVersion;
    }
    
    /**
     * Return input form value for searchfields in list by shop-version
     *
     * @return string
     */
    public function fcGetWhereValue($sTable, $sField) {
        $aWhere = $this->getListFilter();
        if($this->getCurrentVersion() >= 4500) {
            return $aWhere[$sTable][$sField];
        }
        return $aWhere[$sTable.'.'.$sField];
    }
    
    /**
     * Return needed javascript for sorting in list by shop-version
     *
     * @return string
     */
    public function fcGetSortingJavascript($sTable, $sField) {
        if($this->getCurrentVersion() >= 4500) {
            return "Javascript:top.oxid.admin.setSorting( document.search, '{$sTable}', '{$sField}', 'asc');document.search.submit();";
        }
        return "Javascript:document.search.sort.value='{$sTable}.{$sField}';document.search.submit();";
    }

}