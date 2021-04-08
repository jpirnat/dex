<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\Extractors\LeadsFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final class LeadsFileParser
{
	private ShowdownPokemonRepositoryInterface $showdownPokemonRepository;
	private LeadsFileExtractor $leadsFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepositoryInterface $showdownPokemonRepository
	 * @param LeadsFileExtractor $leadsFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		LeadsFileExtractor $leadsFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->leadsFileExtractor = $leadsFileExtractor;
	}

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

		Utils::readLine($stream); // Total leads.
		Utils::readLine($stream); // Separator.
		Utils::readLine($stream); // Column headings.
		Utils::readLine($stream); // Separator.

		while ($this->leadsFileExtractor->isLeadUsage($line = Utils::readLine($stream))) {
			$leadUsage = $this->leadsFileExtractor->extractLeadUsage($line);
			$showdownPokemonName = $leadUsage->showdownPokemonName();

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}
		}
	}
}
