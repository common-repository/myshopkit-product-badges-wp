<?php

namespace MyShopKitMBWP\Automatic\Services\Post;

use Exception;
use MyShopKitMBWP\DefaultBadge\Services\Post\PostService;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Shared\Post\IService;
use MyShopKitMBWP\Shared\Post\TraitMaybeAssertion;
use MyShopKitMBWP\Shared\Post\TraitMaybeSanitizeCallback;


class CreatePostService extends PostService implements IService {
	use TraitDefinePostFields;
	use TraitMaybeAssertion;
	use TraitMaybeSanitizeCallback;

	/**
	 * @throws Exception
	 */
	public function validateFields(): IService {
		foreach ( $this->defineFields() as $friendlyKey => $aField ) {
			if ( isset( $aField['isReadOnly'] ) ) {
				$this->aData[ $aField['key'] ] = $aField['value'];
			} else {
				$value = '';
				if ( isset( $this->aRawData[ $friendlyKey ] ) ) {
					$value = $this->aRawData[ $friendlyKey ];
				} else if ( isset( $aField['value'] ) ) {
					$value = $aField['value'];
				}

				$aAssertionResponse = $this->maybeAssert( $aField, $value );
				if ( $aAssertionResponse['status'] === 'error' ) {
					throw new \Exception( $aAssertionResponse['message'] );
				}
				$this->aData[ $aField['key'] ] = $this->maybeSanitizeCallback( $aField, $value );
			}
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function performSaveData(): array {
		try {
			$this->validateFields();
			$aData = $this->aData;
			unset( $aData['ID'] );
			$id = wp_insert_post( $aData );
			if ( is_wp_error( $id ) ) {
				return MessageFactory::factory()->error( $id->get_error_message(), $id->get_error_code() );
			}

			return MessageFactory::factory()->success(
				esc_html__( 'Congrats! The Automatic has been created successfully.', 'myshopkit-product-badges-wp' ),
				[
					'id' => (string) $id
				]
			);
		}
		catch ( \Exception $oException ) {
			return MessageFactory::factory()->error( $oException->getMessage(), $oException->getCode() );
		}
	}

	public function setRawData( array $aRawData ): IService {
		$this->aRawData = $aRawData;

		return $this;
	}
}
