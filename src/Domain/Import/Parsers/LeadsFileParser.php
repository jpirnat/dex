<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use Jp\Dex\Domain\Import\Extractors\LeadsFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final class LeadsFileParser
{
	public function __construct(
		private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		private LeadsFileExtractor $leadsFileExtractor,
	) {}

	/**
	 * Parse leads data from the given file.
	 *
	 * @param StreamInterface $stream
	 *
	 * @return void
	 */
	public function parse(StreamInterface $stream) : void
	{
		// If the file is empty, there's nothing to parse.
		if ($stream->getSize() === 0) {
			return;
		}

		\GuzzleHttp\Psr7\readline($stream); // Total leads.
		\GuzzleHttp\Psr7\readline($stream); // Separator.
		\GuzzleHttp\Psr7\readline($stream); // Column headings.
		\GuzzleHttp\Psr7\readline($stream); // Separator.

		while ($this->leadsFileExtractor->isLeadUsage($line = \GuzzleHttp\Psr7\readline($stream))) {
			$leadUsage = $this->leadsFileExtractor->extractLeadUsage($line);
			$showdownPokemonName = $leadUsage->showdownPokemonName();

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}
		}
	}
}
