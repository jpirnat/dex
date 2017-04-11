<?php
declare(strict_types=1);

namespace Jp\Dex\Domain;

abstract class EntityId
{
	/** @var int $id */
	protected $id;

	/**
	 * Constructor.
	 *
	 * @param int $id
	 */
	public function __construct(int $id)
	{
		$this->id = $id;
	}

	/**
	 * Get the id's value.
	 *
	 * @return int
	 */
	public function value() : int
	{
		return $this->id;
	}
}
