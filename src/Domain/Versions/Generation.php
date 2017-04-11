<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

class Generation
{
	/** @var int $value */
	protected $value;

	/**
	 * Constructor.
	 *
	 * @param int $value
	 */
	public function __construct(int $value)
	{
		$this->value = $value;
	}

	/**
	 * Get the generation's value.
	 *
	 * @return int
	 */
	public function value() : int
	{
		return $this->value;
	}
}
