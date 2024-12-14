<?php

namespace Palasthotel\ALittleMoreSecure;

use Palasthotel\ALittleMoreSecure\Components\Component;

class Environment extends Component {

	public function getParamName(): string {
		return apply_filters(
			Plugin::FILTER_GET_PARAM_NAME,
			Plugin::DEFAULT_GET_PARAM_NAME
		);
	}

	public function getWaitForSeconds(): int {
		return apply_filters(
			Plugin::FILTER_REDIRECT_WAIT_SECONDS,
			Plugin::DEFAULT_REDIRECT_WAIT_SECONDS
		);
	}

}
