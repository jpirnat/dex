<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Extractors;

use Exception;
use Jp\Trendalyzer\Importers\Structs\YearMonth;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class YearMonthExtractor
{
	/**
	 * Extract a year and month from a stats directory or filename.
	 *
	 * @param string $filename
	 *
	 * @throws Exception if $filename is invalid
	 *
	 * @return YearMonth
	 */
	public function extractYearMonth(string $filename) : YearMonth
	{
		$pattern = '/stats\/(\d+)-(\d+)/';

		try {
			$matchResult = Regex::match($pattern, $filename);

			return new YearMonth(
				(int) $matchResult->group(1),
				(int) $matchResult->group(2)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Filename is invalid: ' . $filename);
		}
	}
}
