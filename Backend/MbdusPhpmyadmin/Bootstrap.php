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
	 * Create events subscriptions
	 *
	 * @return void
	 */
	protected function registerEvents() {

		$this->subscribeEvent ( 'Enlight_Controller_Dispatcher_ControllerPath_Backend_MbdusPhpmyadmin', 'onGetControllerPathBackend' );
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
		return '1.0.0';
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