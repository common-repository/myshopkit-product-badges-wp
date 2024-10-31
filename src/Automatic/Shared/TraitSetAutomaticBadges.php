<?php

namespace MyShopKitMBWP\Automatic\Shared;

trait TraitSetAutomaticBadges
{
	private ?string $userID;

	public function setUserID($userID)
	{
		$this->userID = (string)$userID;
		return $this;
	}

	public function getUserID(): string
	{
		return $this->userID;
	}

	private array $aAutomaticBadgesOrder
		= [
			'new_arrival'  => 'MyShopKitMBWP\Automatic\Shared\NewArrivalBadge',
			'out_of_stock' => 'MyShopKitMBWP\Automatic\Shared\OutOfStockBadge',
			'on_sale'      => 'MyShopKitMBWP\Automatic\Shared\OnSaleBadge'
		];


	public function getPriorityAutomaticBadgesOrder(): array
	{
			return apply_filters(MYSHOPKIT_MB_WP_HOOK_PREFIX . 'Filter/MSKMBWP/src/Automatic/Shared/TraitSetAutomaticBadges',
				array_values($this->aAutomaticBadgesOrder),
				$this->aAutomaticBadgesOrder,
				$this->userID
			);
	}
}
