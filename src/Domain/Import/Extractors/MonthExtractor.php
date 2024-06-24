<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use DateTime;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidFilenameException;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final readonly class MonthExtractor
{
	/**
	 * Extract the month from a stats directory or filename.
	 *
	 * @throws InvalidFilenameException if $filename is invalid.
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
				1,
			);
			return $month;
		} catch (RegexFailed) {
			throw new InvalidFilenameException(
				"Filename is invalid for year-month: $filename"
			);
		}
	}
}
