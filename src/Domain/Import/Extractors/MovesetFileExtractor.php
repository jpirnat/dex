<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidAverageWeightLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidCounterLine1Exception;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidCounterLine2Exception;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidNamePercentLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidPokemonNameLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidRawCountLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidSpreadLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidViabilityCeilingLineException;
use Jp\Dex\Domain\Import\Structs\Counter;
use Jp\Dex\Domain\Import\Structs\Counter1;
use Jp\Dex\Domain\Import\Structs\Counter2;
use Jp\Dex\Domain\Import\Structs\NamePercent;
use Jp\Dex\Domain\Import\Structs\Spread;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final readonly class MovesetFileExtractor
{
	/**
	 * Is this line a separator?
	 */
	public function isSeparator(string $line) : bool
	{
		$pattern = '/---/';

		try {
			$matchResult = Regex::match($pattern, $line);
		} catch (RegexFailed) {
			return false;
		}

		return $matchResult->hasMatch();
	}

	/**
	 * Extract a Pokémon's name from a block header line in the moveset file.
	 *
	 * @throws InvalidPokemonNameLineException if $line is invalid.
	 */
	public function extractPokemonName(string $line) : string
	{
		$pattern = "/\|\s*(\w[\w '.%:-]*?)\s*\|/";

		try {
			$matchResult = Regex::match($pattern, $line);

			return $matchResult->group(1);
		} catch (RegexFailed) {
			throw new InvalidPokemonNameLineException(
				"Pokémon name line is invalid: $line"
			);
		}
	}

	/**
	 * Extract a Pokémon's raw count from a line in the moveset file.
	 *
	 * @throws InvalidRawCountLineException if $line is invalid.
	 */
	public function extractRawCount(string $line) : int
	{
		$pattern = '/Raw count: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed) {
			throw new InvalidRawCountLineException(
				"Raw count line is invalid: $line"
			);
		}
	}

	/**
	 * Extract a Pokémon's average weight from a line in the moveset file.
	 *
	 * @throws InvalidAverageWeightLineException if $line is invalid.
	 */
	public function extractAverageWeight(string $line) : float
	{
		$pattern = '/Avg\. weight: ([\d.e-]+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (float) $matchResult->group(1);
		} catch (RegexFailed) {
			throw new InvalidAverageWeightLineException(
				"Average weight line is invalid: $line"
			);
		}
	}

	/**
	 * Is this line a Viability Ceiling?
	 */
	public function isViabilityCeiling(string $line) : bool
	{
		try {
			$this->extractViabilityCeiling($line);
			return true;
		} catch (InvalidViabilityCeilingLineException) {
			// It must not be a viability ceiling.
		}

		return false;
	}

	/**
	 * Extract a Pokémon's viability ceiling from a line in the moveset file.
	 *
	 * @throws InvalidViabilityCeilingLineException if $line is invalid.
	 */
	public function extractViabilityCeiling(string $line) : int
	{
		$pattern = '/Viability Ceiling: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed) {
			throw new InvalidViabilityCeilingLineException(
				"Viability Ceiling line is invalid: $line"
			);
		}
	}

	/**
	 * Is this line a name and percent?
	 */
	public function isNamePercent(string $line) : bool
	{
		try {
			$this->extractNamePercent($line);
			return true;
		} catch (InvalidNamePercentLineException) {
			// It must not be a name and percent.
		}

		return false;
	}

	/**
	 * Extract a name and percent from a line in the moveset file.
	 *
	 * @throws InvalidNamePercentLineException if $line is invalid.
	 */
	public function extractNamePercent(string $line) : NamePercent
	{
		$pattern = "/(\w[\w '.%:()-]*?)\s+([+-]?[\d.]+)%/";

		try {
			$matchResult = Regex::match($pattern, $line);

			return new NamePercent(
				$matchResult->group(1),
				(float) $matchResult->group(2),
			);
		} catch (RegexFailed) {
			throw new InvalidNamePercentLineException(
				"NamePercent line is invalid: $line"
			);
		}
	}

	/**
	 * Extract an EV spread from a line in the moveset file.
	 *
	 * @throws InvalidSpreadLineException if $line is invalid.
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
				(float) $matchResult->group(8),
			);
		} catch (RegexFailed) {
			throw new InvalidSpreadLineException(
				"Spread line is invalid: $line"
			);
		}
	}

	/**
	 * Is the line a counter line 1?
	 */
	public function isCounter1(string $line1) : bool
	{
		try {
			$this->extractCounter1($line1);
			return true;
		} catch (InvalidCounterLine1Exception) {
			// It must not be a counter line 1.
		}

		return false;
	}

	/**
	 * Extract a counter and statistics from lines in the moveset file.
	 *
	 * @throws InvalidCounterLine1Exception if $line1 is invalid.
	 * @throws InvalidCounterLine2Exception if $line2 is invalid.
	 */
	public function extractCounter(string $line1, string $line2) : Counter
	{
		$counter1 = $this->extractCounter1($line1);
		$counter2 = $this->extractCounter2($line2);

		return new Counter(
			$counter1->showdownPokemonName,
			$counter1->number1,
			$counter1->number2,
			$counter1->number3,
			$counter2->percentKnockedOut,
			$counter2->percentSwitchedOut,
		);
	}

	/**
	 * Extract a counter's line 1 data from a line in the moveset file.
	 *
	 * @throws InvalidCounterLine1Exception if $line1 is invalid.
	 */
	private function extractCounter1(string $line1) : Counter1
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
				(float) $matchResult1->group(4),
			);
		} catch (RegexFailed) {
			throw new InvalidCounterLine1Exception(
				"Counter line 1 is invalid: $line1"
			);
		}
	}

	/**
	 * Extract a counter's line 2 data from a line in the moveset file.
	 *
	 * @throws InvalidCounterLine2Exception if $line2 is invalid.
	 */
	private function extractCounter2(string $line2) : Counter2
	{
		$pattern2 = '/'
			. '([\d.]+)% KOed \/ '      // Percent Knocked Out
			. '([\d.]+)% switched out/' // Percent Switched Out
		;

		try {
			$matchResult2 = Regex::match($pattern2, $line2);

			return new Counter2(
				(float) $matchResult2->group(1),
				(float) $matchResult2->group(2),
			);
		} catch (RegexFailed) {
			throw new InvalidCounterLine2Exception(
				"Counter line 2 is invalid: $line2"
			);
		}
	}

	/**
	 * Is this line a NamePercent with name "Other"?
	 */
	public function isOther(string $line) : bool
	{
		try {
			$namePercent = $this->extractNamePercent($line);
			if ($namePercent->showdownName === 'Other') {
				return true;
			}
		} catch (InvalidNamePercentLineException) {
			// It must not be a NamePercent.
		}

		return false;
	}
}
