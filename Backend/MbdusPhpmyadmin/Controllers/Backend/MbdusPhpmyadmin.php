<?php
/**
 * MbdusPhpmyadmin Plugin - Phpmyadmin Backend Controller
 *
 * @package   Shopware\Plugins\MbdusPhpmyadmin\Controllers\Backend
 */
class Shopware_Controllers_Backend_MbdusPhpmyadmin extends Shopware_Controllers_Backend_ExtJs
{
	/**
	 * Inits ACL-Permissions
	 */
	protected function initAcl() {
		$this->addAclPermission ( 'read', 'Insufficient Permissions' );
	}
	
	/**
	 * index action for initial the phpmyadmin script
	 */
	public function indexAction(){
		$userId = Shopware()->Auth()->getIdentity()->id;
		$apiKey = Shopware()->Db()->fetchOne('SELECT apiKey FROM s_core_auth WHERE id=?',array($userId));
		if(isset($apiKey)){
			$url = 'http://'.$this->getBasePath().'/engine/Shopware/Plugins/Community/Backend/MbdusPhpmyadmin/Components/phpMyAdmin/login.php?apiKey='.$apiKey;
		}
		else{
			$url = 'http://'.$this->getBasePath().'/engine/Shopware/Plugins/Community/Backend/MbdusPhpmyadmin/Components/phpMyAdmin/login.php';
		}
		$path = realpath(dirname(__FILE__));
		$filepath = $path . '/../../Components/phpMyAdmin/config.inc.php';
		chmod($filepath, 0775);
		header('Location: '.$url);
		exit;
	}

	/**
	 * @return mixed
	 */
	public function getBasePath(){
		return Shopware()->Plugins()->Backend()->MbdusPhpmyadmin()->getBasePath();
	}
}