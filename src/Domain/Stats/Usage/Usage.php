<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final readonly class Usage
{
	use ValidateMonthTrait;

	/**
	 * Constructor.
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $totalBattles is invalid.
	 */
	public function __construct(
		private DateTime $month,
		private FormatId $formatId,
		private int $totalBattles,
	) {
		$this->validateMonth($month);

		if ($totalBattles < 0) {
			throw new InvalidCountException("Invalid number of total battles: $totalBattles.");
		}
	}

	/**
	 * Get the month.
	 */
	public function getMonth() : DateTime
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the total battles.
	 */
	public function getTotalBattles() : int
	{
		return $this->totalBattles;
	}
}
