<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidFilenameException;
use Jp\Dex\Domain\Import\Structs\FormatRating;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final readonly class FormatRatingExtractor
{
	/**
	 * Extract a Pokémon Showdown format name and rating from a Pokémon Showdown
	 * stats filename.
	 *
	 * @throws InvalidFilenameException if $filename is invalid.
	 */
	public function extractFormatRating(string $filename) : FormatRating
	{
		$pattern = '/([A-Za-z0-9]+)-(\d+)/';

		try {
			$matchResult = Regex::match($pattern, $filename);

			return new FormatRating(
				$matchResult->group(1),
				(int) $matchResult->group(2),
			);
		} catch (RegexFailed) {
			throw new InvalidFilenameException(
				"Filename is invalid for format-rating: $filename"
			);
		}
	}
}
