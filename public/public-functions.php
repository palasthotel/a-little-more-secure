<?php

use Palasthotel\ALittleMoreSecure\Plugin;

function a_little_more_secure_nonce_field(){
	Plugin::instance()->gate->nonceField();
}
