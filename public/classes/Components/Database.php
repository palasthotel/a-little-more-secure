<?php


namespace Palasthotel\ALittleMoreSecure\Components;

use wpdb;

abstract class Database {

	protected wpdb $wpdb;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->init();
	}

	/**
	 * initialize table names and other properties
	 */
	abstract function init(): void;

	public function createTables(): void {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	}
}
