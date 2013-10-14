<?php

/**
 * 
 * Adds node in admin navigation
 *
 * @author FATCHIP GmbH | Hendrik Bahr
 * @extend roles_bemain
 */
class fcPayOneRolesBeMain extends fcPayOneRolesBeMain_parent {
    
    /**
     * Add the PAYONE main node to the navigation
     *
     * @return string
	 * @extend render
     */
	public function render() {
		$sReturn = parent::render();
		
        $aDynRights 					= $this->_aViewData['aDynRights'];
        $oRights = $this->getRights();
		$aDynRights['fcpo_admin_title'] = $oRights->getViewRightsIndex( 'fcpo_admin_title' );
        $this->_aViewData['aDynRights'] = $aDynRights;
        
        return $sReturn;
	}
   
}