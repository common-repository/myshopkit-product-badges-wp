<?php

namespace MyShopKitMBWP\Automatic\Shared;

use Exception;

trait TraitDetectNextAutomaticBadge
{
	private ?AutomaticContext $oAutomaticContext;

	/**
	 * @throws Exception
	 */
	private function getNextAutomaticBadge(IAutomaticState $oCurrentAutomaticBadge): IAutomaticState
	{
		$order = $this->detectCurrentAutomaticBadgeOrder($oCurrentAutomaticBadge);
		if ($this->isTheEnd($order)) {
			throw new Exception(esc_html__('The product has not added to any badge', 'myshopkit-product-badges-wp'));
		}
		$aAutomaticBadgesOrder = $this->getPriorityAutomaticBadgesOrder();
		return new $aAutomaticBadgesOrder[$order + 1]($this->oAutomaticContext);
	}

	/**
	 * @throws Exception
	 */
	private function detectCurrentAutomaticBadgeOrder(IAutomaticState $oCurrentAutomaticBadge)
	{
		$order = null;
		foreach ($this->getPriorityAutomaticBadgesOrder() as $order => $className) {
			if ($oCurrentAutomaticBadge instanceof $className) {
				return $order;
			}
		}

		if ($order === null) {
			throw new Exception(esc_html__('The Object does not belong to any Classes',
				'myshopkit-product-badges-wp'));
		}
	}

	private function isTheEnd($order): bool
	{
		if (count($this->getPriorityAutomaticBadgesOrder()) == $order + 1) {
			return true;
		}

		return false;
	}
}
