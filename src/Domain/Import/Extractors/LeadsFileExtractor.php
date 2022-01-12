<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Extractors;

use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidLeadUsageLineException;
use Jp\Dex\Domain\Import\Extractors\Exceptions\InvalidTotalLeadsLineException;
use Jp\Dex\Domain\Import\Structs\LeadUsage;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final class LeadsFileExtractor
{
	/**
	 * Extract the total leads count from the first line in the leads file.
	 *
	 * @throws InvalidTotalLeadsLineException if $line is invalid.
	 */
	public function extractTotalLeads(string $line) : int
	{
		$pattern = '/Total leads: (\d+)/';

		try {
			$matchResult = Regex::match($pattern, $line);

			return (int) $matchResult->group(1);
		} catch (RegexFailed) {
			throw new InvalidTotalLeadsLineException(
				'Total leads line is invalid: ' . $line
			);
		}
	}

	/**
	 * Is this line a lead usage data line?
	 */
	public function isLeadUsage(string $line) : bool
	{
		try {
			$this->extractLeadUsage($line);
			return true;
		} catch (InvalidLeadUsageLineException) {
			// It must not be a lead usage data line.
		}

		return false;
	}

	/**
	 * Extract a Pokémon's lead usage data from a line in the leads file.
	 *
	 * @throws InvalidLeadUsageLineException if $line is invalid.
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
		} catch (RegexFailed) {
			throw new InvalidLeadUsageLineException(
				'Lead usage line is invalid: ' . $line
			);
		}
	}
}
