<?php

namespace MyShopKitMBWP\Automatic\Shared;

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

trait TraitUpdateInterval
{
	public string $metaKeyInterval = 'interval';

	public function handleUpdateInterval(int $postID, int $interval)
	{
		return update_post_meta($postID, AutoPrefix::namePrefix($this->metaKeyInterval), $interval);
	}

	public function getInterval(int $postID): int
	{
		return (int)get_post_meta($postID, AutoPrefix::namePrefix($this->metaKeyInterval), true) ?: 7;
	}
}
