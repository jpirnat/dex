<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Extractors;

use Exception;
use Jp\Trendalyzer\Importers\Structs\LeadUsage;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class LeadsFileExtractor
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
	 * Extract the total leads count from the first line in the leads file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return int
	 */
	public function extractTotalLeads(string $line) : int
	{
		$pattern = '/Total leads: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed $e) {
			throw new Exception('Total leads line is invalid: ' . $line);
		}
	}

	/**
	 * Extract a Pokémon's lead usage data from a line in the leads file.
	 *
	 * @param string $line
	 *
	 * @throws Exception if $line is invalid
	 *
	 * @return LeadUsage
	 */
	public function extractLeadUsage(string $line) : LeadUsage
	{
		$pattern = '/\s*\|'
			. '\s*(\d+)\s*\|'             // Rank
			. "\s*(\w[\w '.%:-]*?)\s*\|"  // Pokémon Name
			. '\s*([\d.]+)%\s*\|'         // Usage Percent
			. '\s*(\d+)\s*\|'             // Raw
			. '\s*([\d.]+)%\s*\|'         // Raw Percent
			. '\s*/'
		;

		try {
			$matchResult = Regex::match($pattern, $line);

			return new LeadUsage(
				(int) $matchResult->group(1),
				$matchResult->group(2),
				(float) $matchResult->group(3),
				(int) $matchResult->group(4),
				(float) $matchResult->group(5)
			);
		} catch (RegexFailed $e) {
			throw new Exception('Lead usage line is invalid: ' . $line);
		}
	}
}
