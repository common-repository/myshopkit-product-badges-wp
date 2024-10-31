<?php

namespace MyShopKitMBWP\DefaultBadge\Controllers;

use Exception;

use MyShopKitMBWP\DefaultBadge\Services\Post\BadgeQueryService;
use MyShopKitMBWP\DefaultBadge\Services\Post\CreatePostService;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Illuminate\Upload\WPUpload;
use MyShopKitMBWP\DefaultBadge\Shared\Post\BadgeSkeleton;
use WP_REST_Request;

class DefaultBadgeAPIController
{
	private string $baseUrl = 'https://magicbadges.myshopkit.app/vge/magic-badges/v1/default-badges';

	public function __construct()
	{
		add_action('rest_api_init', [$this, 'registerRoute']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'getDefaultBadges', [$this, 'ajaxGetDefaultBadges']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'loadMoreDefaultBadges', [$this, 'ajaxGetDefaultBadges']);
	}

	public function ajaxGetDefaultBadges()
	{
		$aParams = $_POST['params'] ?? [];
		$oRequest = new WP_REST_Request();
		if (!empty($aParams)) {
			foreach ($aParams as $key => $val) {
				$oRequest->set_param($key, $val);
			}
		}

		$oResponse = $this->getBadges($oRequest);
		MessageFactory::factory('ajax')->success(
			$oResponse->get_data()['message'],
			$oResponse->get_data()['data']
		);
	}

	public function registerRoute()
	{
		register_rest_route(MYSHOPKIT_MB_WP_REST, 'default-badges', [
			[
				'methods'             => 'GET',
				'callback'            => [$this, 'getBadges'],
				'permission_callback' => '__return_true'
			],
			[
				'methods'             => 'POST',
				'callback'            => [$this, 'createBadges'],
				'permission_callback' => '__return_true'
			]

		]);
	}

	public function getBadges(WP_REST_Request $oRequest)
	{
		try {
			$limit = $oRequest->get_param('limit') ?? 24;
			$search = $oRequest->get_param('s');
			$page = $oRequest->get_param('page') ?? 1;

			if (empty(get_current_user_id())) {
				throw new Exception(esc_html__('You must be logged in before performing this function',
					'myshopkit-product-badges-wp'), 401);
			}
			$request = wp_remote_get($this->baseUrl, [
				'body'    => [
					'limit'  => $limit,
					'search' => $search,
					'page'   => $page
				],
				'headers' => [
					'shopName' => 'magic-badge-wp'
				]
			]);
			$aResponse = json_decode(wp_remote_retrieve_body($request), true);

			if (empty($aData = $aResponse['data']['items'])) {
				return MessageFactory::factory('rest')->success(esc_html__('We not found badge',
					'myshopkit-product-badges-wp'), [
					'items'   => $aData,
					'maxPage' => 0
				]);
			}
			return MessageFactory::factory('rest')->success(sprintf(esc_html__('We found %d badges',
				'myshopkit-product-badges-wp'), count($aData)),
				[
					'items'   => $aData,
					'maxPage' => $aResponse['data']['maxPage']
				]);
		}
		catch (Exception $exception) {
			return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
		}
	}

	public function createBadges(WP_REST_Request $oRequest)
	{
		try {
			$aKeywords = array_map(function ($keyword) {
				return trim($keyword);
			}, explode(',', $oRequest->get_param('keywords')));

			if (get_current_user_id() != 1) {
				throw new Exception(esc_html__('You must be logged in before performing this function',
					'myshopkit-product-badges-wp'), 401);
			}
			$oUpload = new WPUpload();
			$aFileInfo = $oRequest->get_file_params();

			$isSingular = isset($aFileInfo['tmp_name']);

			if (empty($aFileInfo)) {
				return MessageFactory::factory('rest')
					->error(
						esc_html__('The file is required', 'myshopkit-product-badges-wp'),
						422
					);
			}

			$oUpload->isSingleUpload($isSingular)
				->setFile($aFileInfo);

			if (!empty($source)) {
				$oUpload->setImageSource($source);
			}

			$aResponse = $oUpload->processUpload();
			if ($aResponse['status'] == 'error') {
				return MessageFactory::factory('rest')->error(
					$aResponse['message'], $aResponse['code']
				);
			}

			$attachmentId = $aResponse['data']['items'][0]['id'];
			$aPostResponse = (new CreatePostService())->setRawData([
				'post_title' => uniqid('magic_badge_wp'),
				'postType'   => $this->postType
			])->performSaveData();

			if ($aPostResponse['status'] == 'error') {
				throw new Exception($aPostResponse['message'], $aPostResponse['code']);
			}
			$postID = $aPostResponse['data']['id'];
			set_post_thumbnail($postID, $attachmentId);
			wp_set_object_terms($postID, $aKeywords, $this->taxonomy);
			return MessageFactory::factory('rest')->success($aPostResponse['message'], [
				'id' => $postID
			]);
		}
		catch (Exception $exception) {
			return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
		}
	}
}
