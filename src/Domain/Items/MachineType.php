<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

class MachineType
{
	/** @var string $TM */
	public const TM = 'tm';

	/** @var string $HM */
	public const HM = 'hm';

	/** @var string $TR */
	public const TR = 'tr';

	/** @var string $value */
	private $value;

	/**
	 * Constructor.
	 *
	 * @param string $value
	 *
	 * @throws Exception if $value is invalid.
	 */
	public function __construct(string $value)
	{
		if ($value !== self::TM && $value !== self::HM && $value !== self::TR) {
			throw new Exception("Invalid machine type given: $value.");
		}

		$this->value = $value;
	}

	/**
	 * Get the machine type's value.
	 *
	 * @return string
	 */
	public function value() : string
	{
		return $this->value;
	}
}
