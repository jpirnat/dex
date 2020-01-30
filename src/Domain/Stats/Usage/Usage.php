<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

final class Usage
{
	use ValidateMonthTrait;

	private DateTime $month;
	private FormatId $formatId;
	private int $totalBattles;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $totalBattles
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidCountException if $totalBattles is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $totalBattles
	) {
		$this->validateMonth($month);

		if ($totalBattles < 0) {
			throw new InvalidCountException(
				'Invalid number of total battles: ' . $totalBattles
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->totalBattles = $totalBattles;
	}

	/**
	 * Get the month.
	 *
	 * @return DateTime
	 */
	public function getMonth() : DateTime
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the total battles.
	 *
	 * @return int
	 */
	public function getTotalBattles() : int
	{
		return $this->totalBattles;
	}
}
