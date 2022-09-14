<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;

final class MonthControlFormatter
{
	/**
	 * Format the prev month or next month for the month control.
	 */
	public function format(?DateTime $month, IntlFormatter $formatter) : ?array
	{
		if ($month === null) {
			return $month;
		}

		return [
			'value' => $month->format('Y-m'),
			'name' => $formatter->formatMonth($month),
		];
	}
}
