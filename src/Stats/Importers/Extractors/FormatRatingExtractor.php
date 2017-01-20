<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Extractors;

use Exception;
use Jp\Dex\Stats\Importers\Structs\FormatRating;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class FormatRatingExtractor
{
	/**
	 * Extract a Pokémon Showdown format name and rating from a Pokémon Showdown
	 * stats filename.
	 *
	 * @param string $filename
	 *
	 * @throws Exception if $filename is invalid
	 *
	 * @return FormatRating
	 */
	public function extractFormatRating(string $filename) : FormatRating
	{
		$pattern = '/([A-Za-z0-9]+)-(\d+)/';

		try {
			$matchResult = Regex::match($pattern, $filename);

			return new FormatRating(
				$matchResult->group(1),
				(int) $matchResult->group(2)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Filename is invalid for format-rating: ' . $filename);
		}
	}
}
