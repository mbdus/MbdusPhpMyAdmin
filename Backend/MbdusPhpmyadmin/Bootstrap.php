<?php
/**
 * Plugin-Bootstrap for PhpMyAdmin
 * @package MbdusPhpmyadmin
 * @subpackage Bootstrap
 * @version 1.0.0
 * @author Mathias Bauer <info@mbdus.de>
 */
class Shopware_Plugins_Backend_MbdusPhpmyadmin_Bootstrap extends Shopware_Components_Plugin_Bootstrap {

	/**
	 * Install plugin method
	 *
	 * @return bool
	 */
	public function install() {
		try {
			$this->registerEvents ();
		} catch ( Exception $exc ) {
			return array (
					'success' => false,
					'message' => $exc->getMessage ()
			);
		}
		return array (
				'success' => true,
				'invalidateCache' => array (
						'backend'
				)
		);
		return true;
	}
	
	/**
	 * Update plugin method
	 *
	 * @return bool
	 */
	public function update($oldVersion) {
		$this->registerEvents ();
		return true;
	}
	
	/**
	 * create acl rights for phpmyadmin
	 */
	public function createAclResource()
	{
		// If exists: find existing MbdusPhpmyadmin resource
		$pluginId = Shopware()->Db()->fetchRow(
				'SELECT pluginID FROM s_core_acl_resources WHERE name = ? ',
				array("mbdusphpmyadmin")
		);
		$pluginId = isset($pluginId['pluginID']) ? $pluginId['pluginID'] : null;
	
		if ($pluginId) {
			// prevent creation of new acl resource
			return;
		}
	
		$resource = new \Shopware\Models\User\Resource();
		$resource->setName('mbdusphpmyadmin');
		$resource->setPluginId($this->getId());
	
		foreach (array('read') as $action) {
			$privilege = new \Shopware\Models\User\Privilege();
			$privilege->setResource($resource);
			$privilege->setName($action);
	
			Shopware()->Models()->persist($privilege);
		}
	
		Shopware()->Models()->persist($resource);
	
		Shopware()->Models()->flush();
	
		if($this->assertMinimumVersion('5.2')){
	
		}
		else{
			Shopware()->Db()->query(
			'UPDATE s_core_menu SET resourceID = ? WHERE controller = "MbdusPhpmyadmin"',
			array($resource->getId())
			);
		}
	}

	/**
	 * Create events subscriptions
	 *
	 * @return void
	 */
	protected function registerEvents() {

		$this->subscribeEvent ( 'Enlight_Controller_Dispatcher_ControllerPath_Backend_MbdusPhpmyadmin', 'onGetControllerPathBackend' );
		$sql = "SELECT name FROM s_core_menu WHERE name = 'PhpMyAdmin'";
		$name = Shopware ()->Db ()->fetchOne ( $sql );
		if (empty ( $name )) {
			if ($this->assertMinimumVersion ( '5.2' )) {
				$this->createMenuItem(array(
						'label' => 'PhpMyAdmin',
						'controller' => '',
						'class' => 'sprite-ui-scroll-pane-detail',
						'action' => '',
						'active' => 1,
						'onClick' => "window.open('http://".$this->getBasePath()."/backend/MbdusPhpmyadmin','_blank')",
						'parent' => $this->Menu()->findOneBy(['label' => 'Einstellungen'])
				));
			}
			else{
				$parent = $this->Menu()->findOneBy('label', 'Einstellungen');
				$item = $this->createMenuItem(array(
						'label'      => 'PhpMyAdmin',
						'class'      => 'sprite-ui-scroll-pane-detail',
						'active'     => 1,
						'controller' => '',
						'onClick'    => "window.open('http://".$this->getBasePath()."/backend/MbdusPhpmyadmin','_blank')",
						'parent'     => $parent,
						'style'      => '',
						'action' 	 => ''
				));
				$this->Menu()->addItem($item);
				$this->Menu()->save();
			}
		}
		$this->createAclResource();
	}

	/**
	 * function to get the shop basepath
	 *
	 * @return string
	 */
	public function getBasePath() {
		if (Shopware ()->Bootstrap ()->issetResource ( 'Shop' )) {
			$shopId = Shopware ()->Shop ()->getId ();
		} else {
			$sql = Shopware ()->Db ()->select ()->from ( 's_core_shops', 'id' )->where ( 'main_id IS NULL' )->order ( 'id ASC' )->limit ( 1 );
			$shopId = Shopware ()->Db ()->fetchOne ( $sql );
		}
		$sql = '
			SELECT concat(host,IF(base_path IS NULL,"",base_path), IF(base_url IS NULL,"",base_url)) as url
			FROM s_core_shops
			WHERE id=:id';
		$params = array (
				':id' => $shopId
		);
		$url = Shopware ()->Db ()->fetchOne ( $sql, $params );
		return $url;
	}

	/**
	 * Return the controllerpath of this plugin
	 *
	 * @return string
	 */
	public function onGetControllerPathBackend(Enlight_Event_EventArgs $args) {
		return $this->Path () . '/Controllers/Backend/MbdusPhpmyadmin.php';
	}

	/**
	 * Get version of this plugin to display in manager
	 *
	 * @return string
	 */
	public function getVersion() {
		return '1.0.1';
	}

	/**
	 * Get label of this plugin to display in manager
	 *
	 * @return string
	 */
	public function getLabel() {
		return 'Mbdus Phpmyadmin';
	}

	/**
	 * Get information of this plugin to display in manager
	 *
	 * @return array
	 */
	public function getInfo() {
		return array (
				'version' => $this->getVersion (),
				'label' => $this->getLabel (),
				'author' => 'mbdus - Softwareentwicklung',
				'description' => 'PhpMyAdmin für schnellen Zugriff auf die Datenbank (/MbdusPhpMyAdmin an URL h&auml;ngen)',
				'support' => 'info@mbdus.de',
				'link' => 'http://www.mbdus.de'
		);
	}
}