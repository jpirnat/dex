<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Parsers;

use Jp\Dex\Stats\Importers\Extractors\MovesetFileExtractor;
use Jp\Dex\Stats\Repositories\ShowdownAbilityRepository;
use Jp\Dex\Stats\Repositories\ShowdownItemRepository;
use Jp\Dex\Stats\Repositories\ShowdownMoveRepository;
use Jp\Dex\Stats\Repositories\ShowdownNatureRepository;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Psr\Http\Message\StreamInterface;

class MovesetFileParser
{
	/** @var ShowdownPokemonRepository $showdownPokemonRepository */
	protected $showdownPokemonRepository;

	/** @var ShowdownAbilityRepository $showdownAbilityRepository */
	protected $showdownAbilityRepository;

	/** @var ShowdownItemRepository $showdownItemRepository */
	protected $showdownItemRepository;

	/** @var ShowdownNatureRepository $showdownNatureRepository */
	protected $showdownNatureRepository;

	/** @var ShowdownMoveRepository $showdownMoveRepository */
	protected $showdownMoveRepository;

	/** @var MovesetFileExtractor $movesetFileExtractor */
	protected $movesetFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param ShowdownAbilityRepository $showdownAbilityRepository
	 * @param ShowdownItemRepository $showdownItemRepository
	 * @param ShowdownNatureRepository $showdownNatureRepository
	 * @param ShowdownMoveRepository $showdownMoveRepository
	 * @param MovesetFileExtractor $movesetFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepository $showdownPokemonRepository,
		ShowdownAbilityRepository $showdownAbilityRepository,
		ShowdownItemRepository $showdownItemRepository,
		ShowdownNatureRepository $showdownNatureRepository,
		ShowdownMoveRepository $showdownMoveRepository,
		MovesetFileExtractor $movesetFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->showdownAbilityRepository = $showdownAbilityRepository;
		$this->showdownItemRepository = $showdownItemRepository;
		$this->showdownNatureRepository = $showdownNatureRepository;
		$this->showdownMoveRepository = $showdownMoveRepository;
		$this->movesetFileExtractor = $movesetFileExtractor;
	}

	/**
	 * Parse moveset data from the given file.
	 *
	 * @param StreamInterface $stream
	 *
	 * @return void
	 */
	public function parse(
		StreamInterface $stream
	) : void {
		// If the file is empty, there's nothing to parse.
		if ($stream->getSize() === 0) {
			return;
		}

		while (!$stream->eof()) {
			// BLOCK 1 - The Pokémon's name.

			\GuzzleHttp\Psr7\readline($stream); // Separator.
			$line = \GuzzleHttp\Psr7\readline($stream);
			if ($stream->eof()) {
				return;
			}
			$showdownPokemonName = $this->movesetFileExtractor->extractPokemonName($line);

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}

			\GuzzleHttp\Psr7\readline($stream); // Separator.

			// BLOCK 2 - General information.

			\GuzzleHttp\Psr7\readline($stream); // Raw count.
			\GuzzleHttp\Psr7\readline($stream); // Average weight.
			$line = \GuzzleHttp\Psr7\readline($stream); // Viability ceiling OR separator.
			if ($this->movesetFileExtractor->isViabilityCeiling($line)) {
				\GuzzleHttp\Psr7\readline($stream); // Separator.
			}

			// BLOCK 3 - Abilities.

			\GuzzleHttp\Psr7\readline($stream); // "Abilities"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownAbilityName = $namePercent->showdownName();

				// If the ability is unknown, add it to the list of unknown abilities.
				if (!$this->showdownAbilityRepository->isKnown($showdownAbilityName)) {
					$this->showdownAbilityRepository->addUnknown($showdownAbilityName);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 4 - Items.

			\GuzzleHttp\Psr7\readline($stream); // "Items"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownItemName = $namePercent->showdownName();

				// If the item is unknown, add it to the list of unknown items.
				if (!$this->showdownItemRepository->isKnown($showdownItemName)) {
					$this->showdownItemRepository->addUnknown($showdownItemName);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 5 - Spreads.

			\GuzzleHttp\Psr7\readline($stream); // "Spreads"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				// Ignore this line if it's an "Other" percent.
				if ($this->movesetFileExtractor->isOther($line)) {
					continue;
				}

				$spread = $this->movesetFileExtractor->extractSpread($line);
				$showdownNatureName = $spread->showdownNatureName();

				// If the nature is unknown, add it to the list of unknown natures.
				if (!$this->showdownNatureRepository->isKnown($showdownNatureName)) {
					$this->showdownNatureRepository->addUnknown($showdownNatureName);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 6 - Moves.

			\GuzzleHttp\Psr7\readline($stream); // "Moves"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownMoveName = $namePercent->showdownName();

				// If the move is unknown, add it to the list of unknown moves.
				if (!$this->showdownMoveRepository->isKnown($showdownMoveName)) {
					$this->showdownMoveRepository->addUnknown($showdownMoveName);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 7 - Teammates.

			\GuzzleHttp\Psr7\readline($stream); // "Teammates"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownTeammateName = $namePercent->showdownName();

				// If the Pokémon is unknown, add it to the list of unknown Pokémon.
				if (!$this->showdownPokemonRepository->isKnown($showdownTeammateName)) {
					$this->showdownPokemonRepository->addUnknown($showdownTeammateName);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 8 - Counters.

			\GuzzleHttp\Psr7\readline($stream); // "Teammates"
			while (!$this->movesetFileExtractor->isSeparator($line1 = \GuzzleHttp\Psr7\readline($stream))) {
				$line2 = \GuzzleHttp\Psr7\readline($stream);
				$counter = $this->movesetFileExtractor->extractCounter($line1, $line2);
				$showdownCounterName = $counter->showdownPokemonName();

				// If the Pokémon is unknown, add it to the list of unknown Pokémon.
				if (!$this->showdownPokemonRepository->isKnown($showdownCounterName)) {
					$this->showdownPokemonRepository->addUnknown($showdownCounterName);
				}
			}
		}
	}

	/**
	 * Return the list of unknown Pokémon.
	 *
	 * @return string[]
	 */
	public function getUnknownPokemon() : array
	{
		return $this->showdownPokemonRepository->getUnknown();
	}

	/**
	 * Return the list of unknown abilities.
	 *
	 * @return string[]
	 */
	public function getUnknownAbilities() : array
	{
		return $this->showdownAbilityRepository->getUnknown();
	}

	/**
	 * Return the list of unknown items.
	 *
	 * @return string[]
	 */
	public function getUnknownItems() : array
	{
		return $this->showdownItemRepository->getUnknown();
	}

	/**
	 * Return the list of unknown natures.
	 *
	 * @return string[]
	 */
	public function getUnknownNatures() : array
	{
		return $this->showdownNatureRepository->getUnknown();
	}

	/**
	 * Return the list of unknown moves.
	 *
	 * @return string[]
	 */
	public function getUnknownMoves() : array
	{
		return $this->showdownMoveRepository->getUnknown();
	}
}

