<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Import\Extractors;

use Exception;
use Jp\Trendalyzer\Import\Structs\Usage;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class UsageExtractor
{
	/**
	 * Is this line a separator?
	 *
	 * @param string $line
	 *
	 * @return bool
	 */
	public function isSeparator(string $line) : bool
	{
		$pattern = '/---/';

		try {
			$matchResult = Regex::match($pattern, $line);
		} catch (RegexFailed $e) {
			return false;
		}

		return $matchResult->hasMatch();
	}

	/**
	 * Extract the total battles count from the first line in the usage file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return int
	 */
	public function extractTotalBattles(string $line) : int
	{
		$pattern = '/Total battles: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid.');
		}
	}

	/**
	 * Extract the average weight per team from the second line in the usage file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return float
	 */
	public function extractAverageWeightPerTeam(string $line) : float
	{
		$pattern = '/Avg. weight\/team: ([\d.]+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (float) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid.');
		}
	}

	/**
	 * Extract a Pokémon's usage data from a line in the usage file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return Usage
	 */
	public function extractUsage(string $line) : Usage
	{
		$pattern = '/\s*\|'
			. '\s*(\d+)\s*\|'     // Rank
			. '\s*([\w-]+)\s*\|'  // Pokémon Name
			. '\s*([\d.]+)%\s*\|' // Usage Percent
			. '\s*(\d+)\s*\|'     // Raw
			. '\s*([\d.]+)%\s*\|' // Raw Percent
			. '\s*([\d.]+)\s*\|'  // Real
			. '\s*([\d.]+)%\s*\|' // Real Percent
			. '\s*/'
		;

		try {
			$matchResult = Regex::match($pattern, $line);

			return new Usage(
				(int) $matchResult->group(1),
				$matchResult->group(2),
				(float) $matchResult->group(3),
				(int) $matchResult->group(4),
				(float) $matchResult->group(5),
				(int) $matchResult->group(6),
				(float) $matchResult->group(7)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid.');
		}
	}
}
