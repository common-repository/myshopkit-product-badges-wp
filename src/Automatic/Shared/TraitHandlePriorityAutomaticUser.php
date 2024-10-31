<?php

namespace MyShopKitMBWP\Automatic\Shared;

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

trait TraitHandlePriorityAutomaticUser
{
	private string $keyPriorityAutomatic = 'priority-automatic';

	public function maybeSaveConfigPriorityAutomaticUser(array $aConfigKey): bool
	{
		return update_option(AutoPrefix::namePrefix($this->keyPriorityAutomatic), $aConfigKey);
	}

	public function getConfigPriorityAutomaticUser(): array
	{
		$aConfig = get_option(AutoPrefix::namePrefix($this->keyPriorityAutomatic));
		return !empty($aConfig) ?$aConfig: [];
	}
}
