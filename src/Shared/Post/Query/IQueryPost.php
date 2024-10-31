<?php


namespace MyShopKitMBWP\Shared\Post\Query;


interface IQueryPost {
	public function setRawArgs( array $aRawArgs ): IQueryPost;

	public function parseArgs(): IQueryPost;

	public function getArgs(): array;

	public function query( PostSkeleton $oPostSkeleton, string $pluck = '', bool $isSingle = false ): array;
}
