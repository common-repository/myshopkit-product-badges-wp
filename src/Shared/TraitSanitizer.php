<?php

namespace MyShopKitMBWP\Shared;

trait TraitSanitizer
{
	private function deepSanitize($val)
	{
		if (!is_array($val)) {
			return sanitize_text_field($val);
		}

		foreach ($val as $key => $subVal) {
			$val[sanitize_text_field($key)] = sanitize_text_field($subVal);
		}

		return $val;
	}
}
