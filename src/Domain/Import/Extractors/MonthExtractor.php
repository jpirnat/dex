<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use DateTime;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidFilenameException;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class MonthExtractor
{
	/**
	 * Extract the month from a stats directory or filename.
	 *
	 * @param string $filename
	 *
	 * @throws InvalidFilenameException if $filename is invalid.
	 *
	 * @return DateTime
	 */
	public function extractMonth(string $filename) : DateTime
	{
		$pattern = '/stats\/(\d+)-(\d+)/';

		try {
			$matchResult = Regex::match($pattern, $filename);

			$month = new DateTime('today');
			$month->setDate(
				(int) $matchResult->group(1),
				(int) $matchResult->group(2),
				1
			);
			return $month;
		} catch (RegexFailed $e) {
			throw new InvalidFilenameException(
				'Filename is invalid for year-month: ' . $filename
			);
		}
	}
}
