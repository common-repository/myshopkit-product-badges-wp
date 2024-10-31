<?php

namespace MyShopKitMBWP\Automatic\Shared;

use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class OnSaleBadge implements IAutomaticState
{
	use TraitSetAutomaticBadges;
	use TraitDetectNextAutomaticBadge;

	private static ?array $aSettings
		= [
			'setting' => 'on_sale_badge'
		];
	protected string      $percentageDiscount = '%percentageDiscount%';
	protected string      $fixedDiscount      = '%fixedDiscount%';
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
			return MessageFactory::factory()->error(
				esc_html__('This product has not added to the On Sale Badge', 'myshopkit-product-badges-wp'),
				400
			);
		}

		if (!$this->isOnSale()) {
			return MessageFactory::factory()->error(
				esc_html__('This product is not on sale', 'myshopkit-product-badges-wp'),
				400
			);
		}

		$this->oAutomaticContext->applyProductBadge(self::$aSettings);

		return MessageFactory::factory()->success(esc_html__('Applied Badge', 'myshopkit-product-badges-wp'));
	}

	private function hasSetting(): bool
	{
		if (self::$aSettings !== null) {
			$aPosts = get_posts([
				'post_type'      => AutoPrefix::namePrefix('on_sale'),
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
		$aConfig = json_decode($jConfig, true);
		$aDataText = [];
		$maxPrice = (int)$this->oAutomaticContext->getProductInfo()['price'][0];
		$minPrice = (int)$this->oAutomaticContext->getProductInfo()['price'][1];
		foreach ($aConfig['texts'] as $aItem) {
			$compare = '';
			if (strpos($aItem['content'], $this->percentageDiscount) !== false) {
				if (!empty($maxPrice) && !empty($abs = abs($maxPrice - $minPrice))) {
					$compare = round(round(($abs / $maxPrice), 3) * 100) . '%';
				}
				$aItem['content'] = str_replace($this->percentageDiscount, $compare, $aItem['content']);
			} elseif (strpos($aItem['content'], $this->fixedDiscount) !== false) {
				if (!empty($maxPrice) && !empty($abs = abs($maxPrice - $minPrice))) {
					$formatMoney = html_entity_decode(get_woocommerce_currency_symbol());
					$compare = $abs.$formatMoney;
				}
				$aItem['content'] = str_replace($this->fixedDiscount, $compare, $aItem['content']);
			}
			$aDataText[] = $aItem;
		}
		$aConfig['texts'] = $aDataText;
		self::$aSettings = [
			'config'   => $aConfig,
			'urlImage' => get_post_meta($aPosts[0]->ID, AutoPrefix::namePrefix('badgeUrl'), true)
		];
		return true;
	}

	/**
	 * https://www.dropbox.com/s/o9julyli0bcftkl/Screen%20Shot%202021-09-17%20at%2012.01.15.png?dl=0
	 *
	 * @return false
	 */
	private function isOnSale(): bool
	{
		return $this->oAutomaticContext->getProductInfo()['onSale'];
	}
}
