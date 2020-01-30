<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use Jp\Dex\Domain\Import\Extractors\MovesetFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final class MovesetFileParser
{
	private ShowdownPokemonRepositoryInterface $showdownPokemonRepository;
	private ShowdownAbilityRepositoryInterface $showdownAbilityRepository;
	private ShowdownItemRepositoryInterface $showdownItemRepository;
	private ShowdownNatureRepositoryInterface $showdownNatureRepository;
	private ShowdownMoveRepositoryInterface $showdownMoveRepository;
	private MovesetFileExtractor $movesetFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepositoryInterface $showdownPokemonRepository
	 * @param ShowdownAbilityRepositoryInterface $showdownAbilityRepository
	 * @param ShowdownItemRepositoryInterface $showdownItemRepository
	 * @param ShowdownNatureRepositoryInterface $showdownNatureRepository
	 * @param ShowdownMoveRepositoryInterface $showdownMoveRepository
	 * @param MovesetFileExtractor $movesetFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
		ShowdownItemRepositoryInterface $showdownItemRepository,
		ShowdownNatureRepositoryInterface $showdownNatureRepository,
		ShowdownMoveRepositoryInterface $showdownMoveRepository,
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
	public function parse(StreamInterface $stream) : void
	{
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
			while ($this->movesetFileExtractor->isNamePercent($line = \GuzzleHttp\Psr7\readline($stream))) {
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
			while ($this->movesetFileExtractor->isNamePercent($line = \GuzzleHttp\Psr7\readline($stream))) {
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
				// If this line is an "Other" percent, skip it.
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
			while ($this->movesetFileExtractor->isNamePercent($line = \GuzzleHttp\Psr7\readline($stream))) {
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
			while ($this->movesetFileExtractor->isNamePercent($line = \GuzzleHttp\Psr7\readline($stream))) {
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

			\GuzzleHttp\Psr7\readline($stream); // "Counters"
			while ($this->movesetFileExtractor->isCounter1($line1 = \GuzzleHttp\Psr7\readline($stream))) {
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
}
