<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Extractors;

use Exception;
use Jp\Trendalyzer\Importers\Structs\Counter;
use Jp\Trendalyzer\Importers\Structs\NamePercent;
use Jp\Trendalyzer\Importers\Structs\Spread;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class MovesetFileExtractor
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
	 * Extract a Pokémon's name from a block header line in the moveset file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return string
	 */
	public function extractPokemonName(string $line) : string
	{
		$pattern = '/\|\s*([\w-]+)\s*\|/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid: ' . $line);
		}
	}

	/**
	 * Extract a Pokémon's raw count from a line in the moveset file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return int
	 */
	public function extractRawCount(string $line) : int
	{
		$pattern = '/Raw count: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid: ' . $line);
		}
	}

	/**
	 * Extract a Pokémon's average weight from a line in the moveset file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return float
	 */
	public function extractAverageWeight(string $line) : float
	{
		$pattern = '/Avg. weight: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid: ' . $line);
		}
	}

	/**
	 * Extract a Pokémon's viability ceiling from a line in the moveset file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return int
	 */
	public function extractViabilityCeiling(string $line) : int
	{
		$pattern = '/Viability Ceiling: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid: ' . $line);
		}
	}

	/**
	 * Extract a name and percent from a line in the moveset file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return NamePercent
	 */
	public function extractNamePercent(string $line) : NamePercent
	{
		$pattern = '/(\w[\w -]*?)\s+([\d.]+)%/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return new NamePercent(
				$matchResult->group(1),
				(float) $matchResult->group(2)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid: ' . $line);
		}
	}

	/**
	 * Extract an EV spread from a line in the moveset file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return Spread
	 */
	public function extractSpreadFromLine(string $line) : Spread
	{
		$pattern = '/'
			. '(\w+):'     // Nature Name
			. '(\d+)\/'    // HP
			. '(\d+)\/'    // Atk
			. '(\d+)\/'    // Def
			. '(\d+)\/'    // SpA
			. '(\d+)\/'    // SpD
			. '(\d+)\s+'   // Spe
			. '([\d.]+)%/' // Percent
		;

		try {
			$matchResult = Regex::match($pattern, $line);

			return new Spread(
				$matchResult->group(1),
				(int) $matchResult->group(2),
				(int) $matchResult->group(3),
				(int) $matchResult->group(4),
				(int) $matchResult->group(5),
				(int) $matchResult->group(6),
				(int) $matchResult->group(7),
				(float) $matchResult->group(8)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Line is invalid: ' . $line);
		}
	}

	/**
	 * Extract a counter and statistics from lines in the moveset file.
	 *
	 * @param string $line1
	 * @param string $line2
	 *
	 * @throws Exception if $line1 is invalid
	 * @throws Exception if $line2 is invalid
	 *
	 * @return Counter
	 */
	public function extractCounterFromLines(string $line1, string $line2) : Counter
	{
		// TODO
	}
}
