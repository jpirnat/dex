<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;

final class MonthControlFormatter
{
	/**
	 * Format the prev month or next month for the month control.
	 *
	 * @param DateTime|null $month
	 * @param IntlFormatter $formatter
	 *
	 * @return array|null
	 */
	public function format(?DateTime $month, IntlFormatter $formatter) : ?array
	{
		if ($month === null) {
			return $month;
		}

		return [
			'month' => $month->format('Y-m'),
			'text' => $formatter->formatMonth($month),
		];
	}
}
