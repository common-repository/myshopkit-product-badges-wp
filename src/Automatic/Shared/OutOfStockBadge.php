<?php

namespace MyShopKitMBWP\Automatic\Shared;


use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;


class OutOfStockBadge implements IAutomaticState
{
	use TraitSetAutomaticBadges;
	use TraitDetectNextAutomaticBadge;

	private static ?array $aSettings
		= [
			'setting' => 'out_of_stock'
		];

	public function __construct(AutomaticContext $oAutomaticContext)
	{
		$this->oAutomaticContext = $oAutomaticContext;
	}

	/**
	 * @throws Exception
	 */
	public function proceedToNextBadge(): IAutomaticState
	{
		$this->oAutomaticContext->setState($this->getNextAutomaticBadge($this));

		return $this;
	}

	public function isAppliedBadge(): bool
	{
		$aResponse = $this->response();

		return $aResponse['status'] == 'success';
	}

	public function response(): array
	{
		if (!$this->hasSetting()) {
			return MessageFactory::factory()->error(esc_html__('This product has not added to the On Sale Badge',
				'myshopkit-product-badges-wp'),
				400);
		}

		if (!$this->isOutOfStock()) {
			return MessageFactory::factory()->error(esc_html__('This product is not on sale',
				'myshopkit-product-badges-wp'),
				400);
		}

		$this->oAutomaticContext->applyProductBadge(self::$aSettings);

		return MessageFactory::factory()->success(esc_html__('Applied Badge', 'myshopkit-product-badges-wp'));
	}

	private function hasSetting(): bool
	{
		if (self::$aSettings !== null) {
			$aPosts = get_posts([
				'post_type'      => AutoPrefix::namePrefix('out_of_stock'),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'author'         => $this->oAutomaticContext->getUserID()
			]);
		}

		if (empty($aPosts)) {
			self::$aSettings = [];
			return false;
		}
		$jConfig = get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('config'), true);
		self::$aSettings = [
			'config'   => json_decode($jConfig, true),
			'urlImage' => get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('badgeUrl'), true)
		];

		return true;
	}

	/**
	 * https://www.dropbox.com/s/9raedhd44b7wxge/Screen%20Shot%202021-09-17%20at%2012.09.58.png?dl=0
	 * @return false
	 */
	private function isOutOfStock(): bool
	{
		$id = (int)$this->oAutomaticContext->getProductInfo()['id'];
		$outOfStock = $this->oAutomaticContext->getProductInfo()['outOfStock'];
		$oProduct = wc_get_product($id);

		if (is_wp_error($oProduct) || empty($oProduct)) {
			return false;
		}

		return ($oProduct->managing_stock() && !$oProduct->is_in_stock()) || $outOfStock;
	}
}
