<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use Exception;
use Jp\Dex\Domain\Import\Structs\Counter;
use Jp\Dex\Domain\Import\Structs\Counter1;
use Jp\Dex\Domain\Import\Structs\Counter2;
use Jp\Dex\Domain\Import\Structs\NamePercent;
use Jp\Dex\Domain\Import\Structs\Spread;
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
		$pattern = "/\|\s*(\w[\w '.%:-]*?)\s*\|/";

		try {
			$matchResult = Regex::match($pattern, $line);

			return $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Pokémon name line is invalid: ' . $line);
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
			throw new Exception('Raw count line is invalid: ' . $line);
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
		$pattern = '/Avg\. weight: ([\d.e-]+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (float) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Average weight line is invalid: ' . $line);
		}
	}

	/**
	 * Is this line a Viability Ceiling?
	 *
	 * @param string $line
	 *
	 * @return bool
	 */
	public function isViabilityCeiling(string $line) : bool
	{
		try {
			$this->extractViabilityCeiling($line);
			return true;
		} catch (Exception $e) {
			// It must not be a viability ceiling.
		}
	
		return false;
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
			throw new Exception('Viability Ceiling line is invalid: ' . $line);
		}
	}

	/**
	 * Is this line a name and percent?
	 *
	 * @param string $line
	 *
	 * @return bool
	 */
	public function isNamePercent(string $line) : bool
	{
		try {
			$this->extractNamePercent($line);
			return true;
		} catch (Exception $e) {
			// It must not be a name and percent.
		}
	
		return false;
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
		$pattern = "/(\w[\w '.%:-]*?)\s+([+-]?[\d.]+)%/";

		try {
			$matchResult = Regex::match($pattern, $line);

			return new NamePercent(
				$matchResult->group(1),
				(float) $matchResult->group(2)
			);
		} catch (RegexFailed $e) {
			throw new Exception('NamePercent line is invalid: ' . $line);
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
	public function extractSpread(string $line) : Spread
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
			throw new Exception('Spread line is invalid: ' . $line);
		}
	}

	/**
	 * Is the line a counter line 1?
	 *
	 * @param string $line1
	 *
	 * @return bool
	 */
	public function isCounter1(string $line1) : bool
	{
		try {
			$this->extractCounter1($line1);
			return true;
		} catch (Exception $e) {
			// It must not be a counter line 1.
		}
	
		return false;
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
	public function extractCounter(string $line1, string $line2) : Counter
	{
		$counter1 = $this->extractCounter1($line1);
		$counter2 = $this->extractCounter2($line2);

		return new Counter(
			$counter1->showdownPokemonName(),
			$counter1->number1(),
			$counter1->number2(),
			$counter1->number3(),
			$counter2->percentKnockedOut(),
			$counter2->percentSwitchedOut()
		);
	}

	/**
	 * Extract a counter's line 1 data from a line in the moveset file.
	 *
	 * @param string $line1
	 *
	 * @throws Exception if $line1 is invalid
	 *
	 * @return Counter1
	 */
	protected function extractCounter1(string $line1) : Counter1
	{
		$pattern1 = '/'
			. "(\w[\w '.%:-]*?) " // Pokémon Name
			. '([\d.]+) '         // number1
			. '\(([\d.]+)'        // number2
			. '±([\d.]+)\)/'      // number3
		;

		try {
			$matchResult1 = Regex::match($pattern1, $line1);

			return new Counter1(
				$matchResult1->group(1),
				(float) $matchResult1->group(2),
				(float) $matchResult1->group(3),
				(float) $matchResult1->group(4)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Counter line 1 is invalid: ' . $line1);
		}
	}

	/**
	 * Extract a counter's line 2 data from a line in the moveset file.
	 *
	 * @param string $line2
	 *
	 * @throws Exception if $line2 is invalid
	 *
	 * @return Counter2
	 */
	protected function extractCounter2(string $line2) : Counter2
	{
		$pattern2 = '/'
			. '([\d.]+)% KOed \/ '      // Percent Knocked Out
			. '([\d.]+)% switched out/' // Percent Switched Out
		;

		try {
			$matchResult2 = Regex::match($pattern2, $line2);

			return new Counter2(
				(float) $matchResult2->group(1),
				(float) $matchResult2->group(2)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Counter line 2 is invalid: ' . $line2);
		}
	}

	/**
	 * Is this line a NamePercent with name "Other"?
	 *
	 * @param string $line
	 *
	 * @return bool
	 */
	public function isOther(string $line) : bool
	{
		try {
			$namePercent = $this->extractNamePercent($line);
			if ($namePercent->showdownName() === 'Other') {
				return true;
			}
		} catch (Exception $e) {
			// It must not be a NamePercent.
		}

		return false;
	}
}
