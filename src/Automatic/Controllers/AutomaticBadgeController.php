<?php

namespace MyShopKitMBWP\Automatic\Controllers;


use Exception;
use MyShopKitMBWP\Automatic\Services\Post\AutomaticQueryService;
use MyShopKitMBWP\Automatic\Services\Post\AutomaticSkeletonService;
use MyShopKitMBWP\Automatic\Services\Post\CreatePostService;
use MyShopKitMBWP\Automatic\Services\Post\DeletePostService;
use MyShopKitMBWP\Automatic\Services\Post\UpdatePostService;
use MyShopKitMBWP\Automatic\Services\PostMeta\AddPostMetaService;
use MyShopKitMBWP\Automatic\Services\PostMeta\UpdatePostMetaService;
use MyShopKitMBWP\Automatic\Shared\AutomaticContext;
use MyShopKitMBWP\Automatic\Shared\TraitHandlePriorityAutomaticUser;
use MyShopKitMBWP\Automatic\Shared\TraitUpdateInterval;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use MyShopKitMBWP\Shared\App;
use MyShopKitMBWP\Shared\Middleware\TraitMainMiddleware;
use MyShopKitMBWP\Shared\Post\TraitIsPostAuthor;
use MyShopKitMBWP\Shared\TraitSanitizer;
use WP_REST_Request;

class AutomaticBadgeController
{
	use TraitSanitizer, TraitMainMiddleware, TraitIsPostAuthor, TraitUpdateInterval, TraitHandlePriorityAutomaticUser,
		TraitHandlePriorityAutomaticUser;

	protected array $aPriority = ['out_of_stock', 'on_sale', 'new_arrival'];

	public function __construct()
	{
		add_filter(
			MYSHOPKIT_MB_WP_HOOK_PREFIX . 'Filter/Automatic/Controllers/AutomaticBadgeController/getAutomaticProduct',
			[$this, 'handleDetectBadge'],
			10,
			2
		);
		add_action('rest_api_init', [$this, 'registerRouters']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'getAutomatics', [$this, 'ajaxGetAutomatics']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'sortAutomatics', [$this, 'ajaxSortAutomatics']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'createBadgeAutomatic', [$this, 'ajaxCreateBadgeAutomatic']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'deleteBadgeAutomatic', [$this, 'ajaxDeleteBadgeAutomatic']);
		add_action('wp_ajax_' . MYSHOPKIT_MB_WP_PREFIX . 'updateBadgeAutomatic', [$this, 'ajaxUpdateBadgeAutomatic']);
	}

	public function ajaxGetAutomatics()
	{
		$aParams = $_POST['params'] ?? [];
		$oRequest = new WP_REST_Request();
		if (!empty($aParams)) {
			foreach ($aParams as $key => $val) {
				$oRequest->set_param(sanitize_text_field($key), $this->deepSanitize($val));
			}
		}

		$oResponse = $this->getAutomatics($oRequest);
		MessageFactory::factory('ajax')->success(
			$oResponse->get_data()['message'],
			$oResponse->get_data()['data']
		);
	}

	public function ajaxSortAutomatics()
	{
		$aParams = $_POST['params'] ?? [];
		$oRequest = new WP_REST_Request();
		if (!empty($aParams)) {
			foreach ($aParams as $key => $val) {
				$oRequest->set_param(sanitize_text_field($key), $this->deepSanitize($val));
			}
		}

		$oResponse = $this->handleUpdatePriorityAutomatic($oRequest);
		MessageFactory::factory('ajax')->success(
			$oResponse->get_data()['message'],
			$oResponse->get_data()['data']
		);
	}

	public function ajaxCreateBadgeAutomatic()
	{
		$aParams = $_POST['params'] ?? [];
		$oRequest = new WP_REST_Request();
		if (!empty($aParams)) {
			foreach ($aParams as $key => $val) {
				$oRequest->set_param(sanitize_text_field($key), $this->deepSanitize($val));
			}
		}

		$oResponse = $this->createAutomatic($oRequest);
		MessageFactory::factory('ajax')->success(
			$oResponse->get_data()['message'],
			$oResponse->get_data()['data']
		);
	}

	public function ajaxDeleteBadgeAutomatic()
	{
		$aParams = $_POST['params'] ?? [];
		$oRequest = new WP_REST_Request();
		if (!empty($aParams)) {
			foreach ($aParams as $key => $val) {
				$oRequest->set_param(sanitize_text_field($key), $this->deepSanitize($val));
			}
		}

		$oResponse = $this->deleteAutomatic($oRequest);
		MessageFactory::factory('ajax')->success(
			$oResponse->get_data()['message'],
			$oResponse->get_data()['data']
		);
	}

	public function ajaxUpdateBadgeAutomatic()
	{
		$aParams = $_POST['params'] ?? [];
		$oRequest = new WP_REST_Request();
		if (!empty($aParams)) {
			foreach ($aParams as $key => $val) {
				$oRequest->set_param(sanitize_text_field($key), $this->deepSanitize($val));
			}
		}

		$oResponse = $this->updateAutomatic($oRequest);
		MessageFactory::factory('ajax')->success(
			$oResponse->get_data()['message'],
			$oResponse->get_data()['data']
		);
	}

	public function registerRouters()
	{
		register_rest_route(MYSHOPKIT_MB_WP_REST, 'automatics',
			[
				[
					'methods'             => 'POST',
					'callback'            => [$this, 'createAutomatic'],
					'permission_callback' => '__return_true'
				],
				[
					'methods'             => 'GET',
					'callback'            => [$this, 'getAutomatics'],
					'permission_callback' => '__return_true'
				]
			]
		);
		register_rest_route(MYSHOPKIT_MB_WP_REST, 'priority-automatic',
			[
				[
					'methods'             => 'PUT',
					'callback'            => [$this, 'handleUpdatePriorityAutomatic'],
					'permission_callback' => '__return_true'
				]
			]
		);
		register_rest_route(MYSHOPKIT_MB_WP_REST, 'automatics/(?P<id>(\d+))',
			[
				[
					'methods'             => 'GET',
					'callback'            => [$this, 'getProduct'],
					'permission_callback' => '__return_true'
				],
				[
					'methods'             => 'PUT',
					'callback'            => [$this, 'updateAutomatic'],
					'permission_callback' => '__return_true'
				],
				[
					'methods'             => 'PATCH',
					'callback'            => [$this, 'updateAutomatic'],
					'permission_callback' => '__return_true'
				],
				[
					'methods'             => 'DELETE',
					'callback'            => [$this, 'deleteAutomatic'],
					'permission_callback' => '__return_true'
				]
			]
		);
	}

	public function createAutomatic(WP_REST_Request $oRequest)
	{
		try {
			$interval = (int)$oRequest->get_param('interval');
			$postType = sanitize_text_field(!empty($oRequest->get_param('postType')) ?
				AutoPrefix::namePrefix($oRequest->get_param('postType')) : '');
			$aResponseMiddleware = $this->processMiddleware(
				[
					'IsUserLoggedIn',
					'IsBadgeTypeExist'
				],
				[
					'postType' => $postType
				]
			);

			if ($aResponseMiddleware['status'] == 'error') {
				throw new Exception($aResponseMiddleware['message'], 401);
			}

			$aCheckUserCreatedAutomatic = $this->checkIsUserCreatedAutomatic(get_current_user_id(), $postType);

			if ($aCheckUserCreatedAutomatic['status'] == 'error') {
				throw new Exception($aCheckUserCreatedAutomatic['message'], 401);
			}
			if ($aCheckUserCreatedAutomatic['data']['status']) {
				throw new Exception(esc_html__('You already set the badge for this type of product.',
					'myshopkit-product-badges-wp'), 401);
			}
			$aPostResponse = (new CreatePostService())->setRawData(array_merge(
				$oRequest->get_params(), [
					'postType' => $postType
				]
			))->performSaveData();

			if ($aPostResponse['status'] == 'error') {
				throw new Exception($aPostResponse['message'], $aPostResponse['code']);
			}

			$postType = AutoPrefix::removePrefix($postType);
			//new arrival
			if ($postType == 'new_arrival' && !empty($interval)) {
				$this->handleUpdateInterval($aPostResponse['data']['id'], $interval);
			}

			$aResponse = (new AddPostMetaService())->setID($aPostResponse['data']['id'])
				->addPostMeta($oRequest->get_params());

			if ($aResponse['status'] == 'error') {
				throw new Exception($aResponse['message'], $aResponse['code']);
			}
			$content = get_post_field('post_content', $aPostResponse['data']['id']);

			return MessageFactory::factory('rest')->success($aPostResponse['message'],
				[
					'id'          => (string)$aPostResponse['data']['id'],
					'date'        => (string)strtotime(get_the_date('Y-m-d H:i:s', $aPostResponse['data']['id'])),
					'description' => $this->handleDescription($postType, $content, [
						'interval' => $interval
					]),
					'interval'    => ($postType == 'new_arrival') ? $interval : '',
				]
			);
		}
		catch (Exception $exception) {
			return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
		}
	}

	public function checkIsUserCreatedAutomatic(string $userID, string $postType)
	{
		$aResponse = (new AutomaticQueryService())->setRawArgs(
			[
				'postType' => $postType,
				'author'   => $userID,
				'limit'    => 1
			]
		)->parseArgs()->query(new AutomaticSkeletonService(), 'id');
		if ($aResponse['status'] == 'error') {
			return MessageFactory::factory()->error($aResponse['message'], 400, [
				'status' => false
			]);
		}
		return MessageFactory::factory()->success('Passed', [
			'status' => !empty($aResponse['data']['items'])
		]);
	}

	public function updateAutomatic(WP_REST_Request $oRequest)
	{
		try {
			$postID = absint($oRequest->get_param('id'));
			$interval = (int)$oRequest->get_param('interval');
			$postType = get_post_type($postID);
			$aResponseMiddleware = $this->processMiddleware(
				[
					'IsUserLoggedIn',
					'IsBadgeTypeExist',
					'IsBadgeExist'
				],
				[
					'postType' => $postType,
					'postID'   => $postID
				]
			);
			if ($aResponseMiddleware['status'] == 'error') {
				throw new Exception($aResponseMiddleware['message'], 401);
			}

			$aPostResponse = (new UpdatePostService())
				->setID($postID)
				->setRawData(array_merge($oRequest->get_params(), [
					'postType' => $postType
				]))
				->performSaveData();

			if ($aPostResponse['status'] == 'error') {
				return MessageFactory::factory('rest')->error($aPostResponse['message'], $aPostResponse['code']);
			}

			$postType = AutoPrefix::removePrefix($postType);
			//new arrival
			if ($postType == 'new_arrival' && !empty($interval)) {
				$this->handleUpdateInterval($aPostResponse['data']['id'], $interval);
			}

			$aResponse = (new UpdatePostMetaService())
				->setID($aPostResponse['data']['id'])
				->updatePostMeta($oRequest->get_params());

			if ($aResponse['status'] == 'error') {
				return MessageFactory::factory('rest')->error($aResponse['message'], $aResponse['code']);
			}
			$content = get_post_field('post_content', $aPostResponse['data']['id']);

			return MessageFactory::factory('rest')
				->success($aPostResponse['message'],
					[
						'id'          => (string)$aPostResponse['data']['id'],
						'date'        => (string)strtotime(get_the_modified_date('Y-m-d H:i:s',
							$aPostResponse['data']['id'])),
						'description' => $this->handleDescription($postType, $content, [
							'interval' => $interval
						]),
						'interval'    => ($postType == 'new_arrival') ? $interval : '',
					]);

		}
		catch (Exception $exception) {
			return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
		}
	}

	private function handleDescription(string $postTypeKey, string $content, array $aMetaFields): string
	{
		$interval = $aMetaFields['interval'];
		switch ($postTypeKey) {
			case'new_arrival' :
				$description = preg_replace_callback('/\d+/',
					function ($aMatches) use ($postTypeKey, $interval) {
						$aMatches[0] = $interval;
						return $aMatches[0];
					}, $content);
				break;
			default :
				$description = $content;
				break;
		}

		return $description;
	}

	public function deleteAutomatic(WP_REST_Request $oRequest)
	{
		try {
			$postID = absint($oRequest->get_param('id'));
			$postType = get_post_type($postID);
			$aResponseMiddleware = $this->processMiddleware(
				[
					'IsUserLoggedIn',
					'IsBadgeTypeExist',
					'IsBadgeExist'
				],
				[
					'postType' => $postType,
					'postID'   => $postID
				]
			);

			$this->isPostAuthor($postID);

			if ($aResponseMiddleware['status'] == 'error') {
				throw new Exception($aResponseMiddleware['message'], 401);
			}
			$aPostResponse = (new DeletePostService())->setID($postID)->setPostType($postType)->delete();
			if ($aPostResponse['status'] == 'error') {
				return MessageFactory::factory('rest')->error($aPostResponse['message'], $aPostResponse['code']);
			}
			return MessageFactory::factory('rest')->success(
				$aPostResponse['message'],
				[
					'id' => $aPostResponse['data']['id'],
					//'urlImage' => get_the_post_thumbnail_url(ThemeOption::getBadgeID(AutoPrefix::removePrefix($postType)))
				]
			);
		}
		catch (Exception $exception) {
			return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
		}
	}

	public function getAutomatics(WP_REST_Request $oRequest)
	{

		$aDataAutomatic = [];
		if (empty($userID = get_current_user_id())) {
			return MessageFactory::factory('rest')
				->error(esc_html__('You must be logged in before performing this function',
					'myshopkit-product-badges-wp'), 401);
		}

		$aData = $this->deepSanitize($oRequest->get_params());
		$aResponse = (new AutomaticQueryService())->setRawArgs(
			array_merge(
				$aData,
				[
					'postType' => array_values(App::get('listPostTypeAutomatic')),
					'author'   => get_current_user_id()
				]
			)
		)->parseArgs()
			->query(new AutomaticSkeletonService(), 'id,title,config,content,urlImage,postType,status');

		if ($aResponse['status'] === 'error') {
			return MessageFactory::factory('rest')->error(
				$aResponse['message'],
				$aResponse['code']
			);
		}


		$aDefaultAuto = include plugin_dir_path(__FILE__) . '../Configs/DefaultAuto.php';
		$aPriority = $this->getConfigPriorityAutomaticUser($userID) ?: $this->aPriority;
		if (!empty($aResponseAutomatic = $aResponse['data']['items'])) {
			foreach ($aResponseAutomatic as $aAutomatic) {
				$postTypeKey = AutoPrefix::removePrefix($aAutomatic['postType']);
				if (in_array($postTypeKey, $aPriority)) {
					$aRawDataAutomatic[$postTypeKey] = [
						'id'          => $aAutomatic['id'],
						'config'      => $aAutomatic['config'],
						'urlImage'    => $aAutomatic['urlImage'],
						'title'       => $aAutomatic['title'],
						'postType'    => $postTypeKey,
						'interval'    => $postTypeKey == 'new_arrival' ? $this->getInterval($aAutomatic['id']) : '',
						'description' => $this->handleDescription($postTypeKey, $aAutomatic['content'], [
							'interval' => ($postTypeKey == 'new_arrival') ? $this->getInterval($aAutomatic['id']) : '',
						]),
						'isSelected'  => $aAutomatic['status'] == 'active'
					];
				}
			}
		}

		foreach ($aPriority as $priority) {
			$aDataAutomatic[$priority] = $aRawDataAutomatic[$priority] ?? $aDefaultAuto[$priority];
		}
		return MessageFactory::factory('rest')->success(esc_html__('we found list badges automatic'), [
			'items' => array_values($aDataAutomatic)
		]);
	}

	public function handleDetectBadge(array $aListProduct, string $userID): array
	{
		$aData = [];
		if (!empty($aListProduct['data']['items'])) {
			foreach ($aListProduct['data']['items'] as $aProduct) {
				$aResponse = $this->detectBadge($aProduct, $userID);
				$aData[$aProduct['id']] = ($aResponse['status'] == 'success') ? $aResponse['data']['badge'] : [];
			}
		}
		return $aData;
	}

	public function handleUpdatePriorityAutomatic(WP_REST_Request $oRequest)
	{

		try {
			if (empty($aPriority = $oRequest->get_param('priority'))) {
				throw new Exception('The param priority is required');
			}

			if (empty(get_current_user_id())) {
				throw new Exception('Sorry, You must log into the App to use this feature',
					'myshopkit-product-badges-wp');
			}

			if (!is_array($aPriority)) {
				$aPriority = explode(',', $aPriority);
			}

			$aPriority = array_map(function ($key) {
				return sanitize_text_field(trim($key));
			}, $aPriority);

			$this->maybeSaveConfigPriorityAutomaticUser(array_unique($aPriority));

			return MessageFactory::factory('rest')->success('Priority updated');
		}
		catch (Exception $exception) {
			return MessageFactory::factory('rest')->error($exception->getMessage(), $exception->getCode());
		}
	}

	public function detectBadge(array $aProduct, $userId = 0)
	{
		$oAutomaticBadgeContext = new AutomaticContext();
		$oAutomaticBadgeContext->setProductInfo($aProduct)->setUserID(0)->setPriority();
		try {
			while (!$oAutomaticBadgeContext->oState->isAppliedBadge()) {
				$oAutomaticBadgeContext->oState->setUserId(0)->proceedToNextBadge();
			}
			return MessageFactory::factory()->success(esc_html__('list data', 'myshopkit-product-badges-wp'),
				$oAutomaticBadgeContext->getProductInfo());
		}
		catch (Exception $oException) {
			return MessageFactory::factory()->error(
				$oException->getMessage(),
				$oException->getCode()
			);
		}
	}
}
