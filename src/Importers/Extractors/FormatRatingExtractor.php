<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Extractors;

use Exception;
use Jp\Trendalyzer\Importers\Structs\FormatRating;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class FormatRatingExtractor
{
	/**
	 * Extract a Smogon format name and rating from a Smogon stats filename.
	 *
	 * @param string $filename
	 *
	 * @throws Exception if $filename is invalid
	 *
	 * @return FormatRating
	 */
	public function extractFormatRating(string $filename) : FormatRating
	{
		$pattern = '/([A-Za-z0-9])-(\d+)/';

		try {
			$matchResult = Regex::match($pattern, $filename);

			return new FormatRating(
				$matchResult->group(1),
				(int) $matchResult->group(2)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Filename is invalid.');
		}
	}
}
