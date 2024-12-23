<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\Extractors\MovesetFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownTypeRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final readonly class MovesetFileParser
{
	public function __construct(
		private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		private ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
		private ShowdownItemRepositoryInterface $showdownItemRepository,
		private ShowdownNatureRepositoryInterface $showdownNatureRepository,
		private ShowdownMoveRepositoryInterface $showdownMoveRepository,
		private ShowdownTypeRepositoryInterface $showdownTypeRepository,
		private MovesetFileExtractor $movesetFileExtractor,
	) {}

	/**
	 * Parse moveset data from the given file.
	 */
	public function parse(StreamInterface $stream) : void
	{
		// If the file is empty, there's nothing to parse.
		if ($stream->getSize() === 0) {
			return;
		}

		while (!$stream->eof()) {
			// BLOCK 1 - The Pokémon's name.

			Utils::readLine($stream); // Separator.
			$line = Utils::readLine($stream);
			if ($stream->eof()) {
				return;
			}
			$showdownPokemonName = $this->movesetFileExtractor->extractPokemonName($line);

			// If the Pokémon is unknown, add it to the list of unknown Pokémon.
			if (!$this->showdownPokemonRepository->isKnown($showdownPokemonName)) {
				$this->showdownPokemonRepository->addUnknown($showdownPokemonName);
			}

			Utils::readLine($stream); // Separator.

			// BLOCK 2 - General information.

			Utils::readLine($stream); // Raw count.
			Utils::readLine($stream); // Average weight.
			$line = Utils::readLine($stream); // Viability ceiling OR separator.
			if ($this->movesetFileExtractor->isViabilityCeiling($line)) {
				Utils::readLine($stream); // Separator.
			}

			// BLOCK 3 - Abilities.

			Utils::readLine($stream); // "Abilities"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownAbilityName = $namePercent->showdownName;

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

			Utils::readLine($stream); // "Items"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownItemName = $namePercent->showdownName;

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

			Utils::readLine($stream); // "Spreads"
			if ($stream->eof()) {
				return;
			}
			while (!$this->movesetFileExtractor->isSeparator($line = Utils::readLine($stream))) {
				// If this line is an "Other" percent, skip it.
				if ($this->movesetFileExtractor->isOther($line)) {
					continue;
				}

				$spread = $this->movesetFileExtractor->extractSpread($line);
				$showdownNatureName = $spread->showdownNatureName;

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

			Utils::readLine($stream); // "Moves"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownMoveName = $namePercent->showdownName;

				// If the move is unknown, add it to the list of unknown moves.
				if (!$this->showdownMoveRepository->isKnown($showdownMoveName)) {
					$this->showdownMoveRepository->addUnknown($showdownMoveName);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 7 (if it exists) - Tera Types.
			$line = Utils::readLine($stream); // "Tera Types"
			if (str_contains($line, 'Tera Types')) {
				if ($stream->eof()) {
					return;
				}
				while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
					$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
					$showdownTypeName = $namePercent->showdownName;

					// If the type is unknown, add it to the list of unknown types.
					if (!$this->showdownTypeRepository->isKnown($showdownTypeName)) {
						$this->showdownTypeRepository->addUnknown($showdownTypeName);
					}

					// If the file randomly ends here, there's nothing else to do.
					if ($stream->eof()) {
						return;
					}
				}

				Utils::readLine($stream); // "Teammates"
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 7 - Teammates.
			while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownTeammateName = $namePercent->showdownName;

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

			Utils::readLine($stream); // "Counters"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isCounter1($line1 = Utils::readLine($stream))) {
				$line2 = Utils::readLine($stream);
				$counter = $this->movesetFileExtractor->extractCounter($line1, $line2);
				$showdownCounterName = $counter->showdownPokemonName;

				// If the Pokémon is unknown, add it to the list of unknown Pokémon.
				if (!$this->showdownPokemonRepository->isKnown($showdownCounterName)) {
					$this->showdownPokemonRepository->addUnknown($showdownCounterName);
				}
			}
		}
	}
}
