<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidAverageWeightPerTeamLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidTotalBattlesLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidUsageLineException;
use Jp\Dex\Domain\Import\Structs\Usage;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class UsageFileExtractor
{
	/**
	 * Extract the total battles count from the first line in the usage file.
	 *
	 * @param string $line
	 *
	 * @throws InvalidTotalBattlesLineException if $line is invalid.
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
			throw new InvalidTotalBattlesLineException(
				'Total battles line is invalid: ' . $line
			);
		}
	}

	/**
	 * Extract the average weight per team from the second line in the usage file.
	 *
	 * @param string $line
	 *
	 * @throws InvalidAverageWeightPerTeamLineException if $line is invalid.
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
			throw new InvalidAverageWeightPerTeamLineException(
				'Average weight per team line is invalid: ' . $line
			);
		}
	}

	/**
	 * Is this line a usage data line?
	 *
	 * @param string $line
	 *
	 * @return bool
	 */
	public function isUsage(string $line) : bool
	{
		try {
			$this->extractUsage($line);
			return true;
		} catch (InvalidUsageLineException $e) {
			// It must not be a usage data line.
		}

		return false;
	}

	/**
	 * Extract a Pokémon's usage data from a line in the usage file.
	 *
	 * @param string $line
	 *
	 * @throws InvalidUsageLineException if $line is invalid.
	 *
	 * @return Usage
	 */
	public function extractUsage(string $line) : Usage
	{
		$pattern = '/\s*\|'
			. '\s*(\d+)\s*\|'            // Rank
			. "\s*(\w[\w '.%:-]*?)\s*\|" // Pokémon Name
			. '\s*([\d.]+)%\s*\|'        // Usage Percent
			. '\s*(\d+)\s*\|'            // Raw
			. '\s*([\d.]+)%\s*\|'        // Raw Percent
			. '\s*([\d.]+)\s*\|'         // Real
			. '\s*([\d.]+)%\s*\|'        // Real Percent
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
			throw new InvalidUsageLineException(
				'Usage line is invalid: ' . $line
			);
		}
	}
}
