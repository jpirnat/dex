<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Parsers;

use Jp\Dex\Stats\Importers\Extractors\LeadsFileExtractor;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Psr\Http\Message\StreamInterface;

class LeadsFileParser
{
	/** @var ShowdownPokemonRepository $showdownPokemonRepository */
	protected $showdownPokemonRepository;

	/** @var LeadsFileExtractor $leadsFileExtractor */
	protected $leadsFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param LeadsFileExtractor $leadsFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepository $showdownPokemonRepository,
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

		\GuzzleHttp\Psr7\readline($stream); // Total leads.
		\GuzzleHttp\Psr7\readline($stream); // Separator.
		\GuzzleHttp\Psr7\readline($stream); // Column headings.
		\GuzzleHttp\Psr7\readline($stream); // Separator.

		while (!$this->leadsFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
			$leadUsage = $this->leadsFileExtractor->extractLeadUsage($line);
			$showdownPokemonName = $leadUsage->showdownPokemonName();

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}
		}
	}
}
