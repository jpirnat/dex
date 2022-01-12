<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\Extractors\UsageFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final class UsageFileParser
{
	public function __construct(
		private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		private UsageFileExtractor $usageFileExtractor,
	) {}

	/**
	 * Parse usage data from the given file.
	 *
	 * @return int Total battles, or -1 if the file is empty.
	 */
	public function parse(StreamInterface $stream) : int
	{
		// If the file is empty, there's nothing to parse.
		if ($stream->getSize() === 0) {
			return -1;
		}

		$line = Utils::readLine($stream); // Total battles.
		$totalBattles = $this->usageFileExtractor->extractTotalBattles($line);
		Utils::readLine($stream); // Average weight per team.
		Utils::readLine($stream); // Separator.
		Utils::readLine($stream); // Column headings.
		Utils::readLine($stream); // Separator.

		while ($this->usageFileExtractor->isUsage($line = Utils::readLine($stream))) {
			$usage = $this->usageFileExtractor->extractUsage($line);
			$showdownPokemonName = $usage->showdownPokemonName();

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}
		}

		return $totalBattles;
	}
}
