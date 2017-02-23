<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Parsers;

use Jp\Dex\Stats\Importers\Extractors\UsageFileExtractor;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Psr\Http\Message\StreamInterface;

class UsageFileParser
{
	/** @var ShowdownPokemonRepository $showdownPokemonRepository */
	protected $showdownPokemonRepository;

	/** @var UsageFileExtractor $usageFileExtractor */
	protected $usageFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param UsageFileExtractor $usageFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepository $showdownPokemonRepository,
		UsageFileExtractor $usageFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->usageFileExtractor = $usageFileExtractor;
	}

	/**
	 * Parse usage data from the given file.
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

		\GuzzleHttp\Psr7\readline($stream); // Total battles.
		\GuzzleHttp\Psr7\readline($stream); // Average weight per team.
		\GuzzleHttp\Psr7\readline($stream); // Separator.
		\GuzzleHttp\Psr7\readline($stream); // Column headings.
		\GuzzleHttp\Psr7\readline($stream); // Separator.

		while (!$this->usageFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
			$usage = $this->usageFileExtractor->extractUsage($line);
			$showdownPokemonName = $usage->showdownPokemonName();

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}
		}
	}
}
