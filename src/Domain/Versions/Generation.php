<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

class Generation
{
	/** @var int $value */
	private $value;

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
	public function getValue() : int
	{
		return $this->value;
	}
}
