<?php
declare(strict_types=1);

namespace Jp\Dex\Domain;

abstract class EntityId
{
	public function __construct(
		protected(set) int $value,
	) {}
}
