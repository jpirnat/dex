<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Exception;

class MachineType
{
	public const TM = 'tm';
	public const HM = 'hm';
	public const TR = 'tr';

	private string $value;

	/**
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
	 */
	public function value() : string
	{
		return $this->value;
	}
}
