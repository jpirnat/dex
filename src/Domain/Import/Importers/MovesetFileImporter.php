<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use DateTime;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Import\Extractors\MovesetFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounter;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpread;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammate;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final readonly class MovesetFileImporter
{
	public function __construct(
		private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		private ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
		private ShowdownItemRepositoryInterface $showdownItemRepository,
		private ShowdownNatureRepositoryInterface $showdownNatureRepository,
		private ShowdownMoveRepositoryInterface $showdownMoveRepository,
		private UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		private MovesetPokemonRepositoryInterface $movesetPokemonRepository,
		private MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		private MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository,
		private MovesetRatedItemRepositoryInterface $movesetRatedItemRepository,
		private MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository,
		private MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		private MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository,
		private MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository,
		private MovesetFileExtractor $movesetFileExtractor,
	) {}

	/**
	 * Import moveset data from the given file.
	 */
	public function import(
		StreamInterface $stream,
		DateTime $month,
		FormatId $formatId,
		int $rating,
	) : void {
		echo 'Importing moveset file: month ' . $month->format('Y-m')
			. ', format id ' . $formatId->value()
			. ", rating $rating. (" . (new DateTime())->format('Y-m-d H:i:s') . ")\n";

		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$movesetPokemonExists = $this->movesetPokemonRepository->hasAny(
			$month,
			$formatId,
		);
		$movesetRatedPokemonExists = $this->movesetRatedPokemonRepository->hasAny(
			$month,
			$formatId,
			$rating,
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if ($movesetPokemonExists
			&& $movesetRatedPokemonExists
		) {
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
			// If this Pokémon is not meant to be imported, skip it.
			$pokemonId = null;
			$usageRatedPokemonId = null;
			if ($this->showdownPokemonRepository->isImported($showdownPokemonName)) {
				$pokemonId = $this->showdownPokemonRepository->getPokemonId($showdownPokemonName);
				$usageRatedPokemonId = $this->usageRatedPokemonRepository->getId(
					$month,
					$formatId,
					$rating,
					$pokemonId,
				);
			}
			Utils::readLine($stream); // Separator.

			// BLOCK 2 - General information.

			$line = Utils::readLine($stream); // Raw count.
			$rawCount = $this->movesetFileExtractor->extractRawCount($line);

			$line = Utils::readLine($stream); // Average weight.
			$averageWeight = $this->movesetFileExtractor->extractAverageWeight($line);

			$line = Utils::readLine($stream); // Viability ceiling OR separator.
			if ($this->movesetFileExtractor->isViabilityCeiling($line)) {
				$viabilityCeiling = $this->movesetFileExtractor->extractViabilityCeiling($line);
				Utils::readLine($stream); // Separator.
			} else {
				$viabilityCeiling = null;
			}

			if ($pokemonId && !$movesetPokemonExists) {
				$movesetPokemon = new MovesetPokemon(
					$month,
					$formatId,
					$pokemonId,
					$rawCount,
					$viabilityCeiling,
				);

				$this->movesetPokemonRepository->save($movesetPokemon);
			}

			if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
				$movesetRatedPokemon = new MovesetRatedPokemon(
					$usageRatedPokemonId,
					$averageWeight,
				);

				$this->movesetRatedPokemonRepository->save($movesetRatedPokemon);
			}

			// BLOCK 3 - Abilities.

			Utils::readLine($stream); // "Abilities"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownAbilityName = $namePercent->showdownName();

				// If this ability is not meant to be imported, skip it.
				if (!$this->showdownAbilityRepository->isImported($showdownAbilityName)) {
					continue;
				}

				$abilityId = $this->showdownAbilityRepository->getAbilityId($showdownAbilityName);

				if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
					$movesetRatedAbility = new MovesetRatedAbility(
						$usageRatedPokemonId,
						$abilityId,
						$namePercent->percent(),
					);

					$this->movesetRatedAbilityRepository->save($movesetRatedAbility);
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
				$showdownItemName = $namePercent->showdownName();

				// If this item is not meant to be imported, skip it.
				if (!$this->showdownItemRepository->isImported($showdownItemName)) {
					continue;
				}

				$itemId = $this->showdownItemRepository->getItemId($showdownItemName);

				if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
					$movesetRatedItem = new MovesetRatedItem(
						$usageRatedPokemonId,
						$itemId,
						$namePercent->percent(),
					);

					$this->movesetRatedItemRepository->save($movesetRatedItem);
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
				$showdownNatureName = $spread->showdownNatureName();

				$natureId = $this->showdownNatureRepository->getNatureId($showdownNatureName);

				if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
					$evSpread = new StatValueContainer([
						new StatValue(new StatId(StatId::HP), $spread->hp()),
						new StatValue(new StatId(StatId::ATTACK), $spread->atk()),
						new StatValue(new StatId(StatId::DEFENSE), $spread->def()),
						new StatValue(new StatId(StatId::SPECIAL_ATTACK), $spread->spa()),
						new StatValue(new StatId(StatId::SPECIAL_DEFENSE), $spread->spd()),
						new StatValue(new StatId(StatId::SPEED), $spread->spe()),
					]);

					$movesetRatedSpread = new MovesetRatedSpread(
						$usageRatedPokemonId,
						$natureId,
						$evSpread,
						$spread->percent(),
					);

					$this->movesetRatedSpreadRepository->save($movesetRatedSpread);
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
				$showdownMoveName = $namePercent->showdownName();

				// If this move is not meant to be imported, skip it.
				if (!$this->showdownMoveRepository->isImported($showdownMoveName)) {
					continue;
				}

				$moveId = $this->showdownMoveRepository->getMoveId($showdownMoveName);

				if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
					$movesetRatedMove = new MovesetRatedMove(
						$usageRatedPokemonId,
						$moveId,
						$namePercent->percent(),
					);

					$this->movesetRatedMoveRepository->save($movesetRatedMove);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 7 - Teammates.

			Utils::readLine($stream); // "Teammates"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isNamePercent($line = Utils::readLine($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownTeammateName = $namePercent->showdownName();

				// If this Pokémon is not meant to be imported, skip it.
				if (!$this->showdownPokemonRepository->isImported($showdownTeammateName)) {
					continue;
				}

				$teammateId = $this->showdownPokemonRepository->getPokemonId($showdownTeammateName);

				if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
					$movesetRatedTeammate = new MovesetRatedTeammate(
						$usageRatedPokemonId,
						$teammateId,
						$namePercent->percent(),
					);

					$this->movesetRatedTeammateRepository->save($movesetRatedTeammate);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 8 - Counters

			Utils::readLine($stream); // "Counters"
			if ($stream->eof()) {
				return;
			}
			while ($this->movesetFileExtractor->isCounter1($line1 = Utils::readLine($stream))) {
				$line2 = Utils::readLine($stream);
				$counter = $this->movesetFileExtractor->extractCounter($line1, $line2);
				$showdownCounterName = $counter->showdownPokemonName();

				// If this Pokémon is not meant to be imported, skip it.
				if (!$this->showdownPokemonRepository->isImported($showdownCounterName)) {
					continue;
				}

				$counterId = $this->showdownPokemonRepository->getPokemonId($showdownCounterName);

				if ($usageRatedPokemonId && !$movesetRatedPokemonExists) {
					$movesetRatedCounter = new MovesetRatedCounter(
						$usageRatedPokemonId,
						$counterId,
						$counter->number1(),
						$counter->number2(),
						$counter->number3(),
						$counter->percentKnockedOut(),
						$counter->percentSwitchedOut(),
					);

					$this->movesetRatedCounterRepository->save($movesetRatedCounter);
				}
			}
		}
	}
}
