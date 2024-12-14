<?php


namespace Palasthotel\ALittleMoreSecure\Components;

abstract class Component {


	public function __construct(
		public \Palasthotel\ALittleMoreSecure\Plugin $plugin
	) {
		$this->onCreate();
	}

	/**
	 * overwrite this method in component implementations
	 */
	public function onCreate(): void {
		// init your hooks and stuff
	}
}
