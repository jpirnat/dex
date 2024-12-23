<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Exception;

final readonly class MachineType
{
	public const string TM = 'tm';
	public const string HM = 'hm';
	public const string TR = 'tr';

	private(set) string $value;

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
}
