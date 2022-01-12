<?php
declare(strict_types=1);

namespace Jp\Dex\Domain;

abstract class EntityId
{
	public function __construct(
		protected int $id,
	) {}

	/**
	 * Get the id's value.
	 */
	public function value() : int
	{
		return $this->id;
	}
}
