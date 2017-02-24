<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use Jp\Dex\Stats\Importers\Extractors\MovesetFileExtractor;
use Jp\Dex\Stats\Repositories\ShowdownAbilityRepository;
use Jp\Dex\Stats\Repositories\ShowdownItemRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetPokemonRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedAbilitiesRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedCountersRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedItemsRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedMovesRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedPokemonRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedSpreadsRepository;
use Jp\Dex\Stats\Repositories\Moveset\MovesetRatedTeammatesRepository;
use Jp\Dex\Stats\Repositories\ShowdownMoveRepository;
use Jp\Dex\Stats\Repositories\ShowdownNatureRepository;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Psr\Http\Message\StreamInterface;

class MovesetFileImporter
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

	/** @var MovesetPokemonRepository $movesetPokemonRepository */
	protected $movesetPokemonRepository;

	/** @var MovesetRatedPokemonRepository $movesetRatedPokemonRepository */
	protected $movesetRatedPokemonRepository;

	/** @var MovesetRatedAbilitiesRepository $movesetRatedAbilitiesRepository */
	protected $movesetRatedAbilitiesRepository;

	/** @var MovesetRatedItemsRepository $movesetRatedItemsRepository */
	protected $movesetRatedItemsRepository;

	/** @var MovesetRatedSpreadsRepository $movesetRatedSpreadsRepository */
	protected $movesetRatedSpreadsRepository;

	/** @var MovesetRatedMovesRepository $movesetRatedMovesRepository */
	protected $movesetRatedMovesRepository;

	/** @var MovesetRatedTeammatesRepository $movesetRatedTeammatesRepository */
	protected $movesetRatedTeammatesRepository;

	/** @var MovesetRatedCountersRepository $movesetRatedCountersRepository */
	protected $movesetRatedCountersRepository;

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
	 * @param MovesetPokemonRepository $movesetPokemonRepository
	 * @param MovesetRatedPokemonRepository $movesetRatedPokemonRepository
	 * @param MovesetRatedAbilitiesRepository $movesetRatedAbilitiesRepository
	 * @param MovesetRatedItemsRepository $movesetRatedItemsRepository
	 * @param MovesetRatedSpreadsRepository $movesetRatedSpreadsRepository
	 * @param MovesetRatedMovesRepository $movesetRatedMovesRepository
	 * @param MovesetRatedTeammatesRepository $movesetRatedTeammatesRepository
	 * @param MovesetRatedCountersRepository $movesetRatedCountersRepository
	 * @param MovesetFileExtractor $movesetFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepository $showdownPokemonRepository,
		ShowdownAbilityRepository $showdownAbilityRepository,
		ShowdownItemRepository $showdownItemRepository,
		ShowdownNatureRepository $showdownNatureRepository,
		ShowdownMoveRepository $showdownMoveRepository,
		MovesetPokemonRepository $movesetPokemonRepository,
		MovesetRatedPokemonRepository $movesetRatedPokemonRepository,
		MovesetRatedAbilitiesRepository $movesetRatedAbilitiesRepository,
		MovesetRatedItemsRepository $movesetRatedItemsRepository,
		MovesetRatedSpreadsRepository $movesetRatedSpreadsRepository,
		MovesetRatedMovesRepository $movesetRatedMovesRepository,
		MovesetRatedTeammatesRepository $movesetRatedTeammatesRepository,
		MovesetRatedCountersRepository $movesetRatedCountersRepository,
		MovesetFileExtractor $movesetFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->showdownAbilityRepository = $showdownAbilityRepository;
		$this->showdownItemRepository = $showdownItemRepository;
		$this->showdownNatureRepository = $showdownNatureRepository;
		$this->showdownMoveRepository = $showdownMoveRepository;
		$this->movesetPokemonRepository = $movesetPokemonRepository;
		$this->movesetRatedPokemonRepository = $movesetRatedPokemonRepository;
		$this->movesetRatedAbilitiesRepository = $movesetRatedAbilitiesRepository;
		$this->movesetRatedItemsRepository = $movesetRatedItemsRepository;
		$this->movesetRatedSpreadsRepository = $movesetRatedSpreadsRepository;
		$this->movesetRatedMovesRepository = $movesetRatedMovesRepository;
		$this->movesetRatedTeammatesRepository = $movesetRatedTeammatesRepository;
		$this->movesetRatedCountersRepository = $movesetRatedCountersRepository;
		$this->movesetFileExtractor = $movesetFileExtractor;
	}

	/**
	 * Import moveset data from the given file.
	 *
	 * @param StreamInterface $stream
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function import(
		StreamInterface $stream,
		int $year,
		int $month,
		int $formatId,
		int $rating
	) : void {
		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$movesetPokemonExists = $this->movesetPokemonRepository->exists(
			$year,
			$month,
			$formatId
		);
		$movesetRatedPokemonExists = $this->movesetRatedPokemonRepository->exists(
			$year,
			$month,
			$formatId,
			$rating
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if (
			$movesetPokemonExists
			&& $movesetRatedPokemonExists
		) {
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
			// If this Pokémon is not meant to be imported, skip it.
			if ($this->showdownPokemonRepository->isImported($showdownPokemonName)) {
				$pokemonId = $this->showdownPokemonRepository->getPokemonId($showdownPokemonName);
				$isPokemonImported = true;
			} else {
				$isPokemonImported = false;
			}
			\GuzzleHttp\Psr7\readline($stream); // Separator.

			// BLOCK 2 - General information.

			$line = \GuzzleHttp\Psr7\readline($stream); // Raw count.
			$rawCount = $this->movesetFileExtractor->extractRawCount($line);

			$line = \GuzzleHttp\Psr7\readline($stream); // Average weight.
			$averageWeight = $this->movesetFileExtractor->extractAverageWeight($line);

			$line = \GuzzleHttp\Psr7\readline($stream); // Viability ceiling OR separator.
			if ($this->movesetFileExtractor->isViabilityCeiling($line)) {
				$viabilityCeiling = $this->movesetFileExtractor->extractViabilityCeiling($line);
				\GuzzleHttp\Psr7\readline($stream); // Separator.
			} else {
				$viabilityCeiling = null;
			}

			if ($isPokemonImported && !$movesetPokemonExists) {
				$this->movesetPokemonRepository->insert(
					$year,
					$month,
					$formatId,
					$pokemonId,
					$rawCount,
					$viabilityCeiling
				);
			}

			if ($isPokemonImported && !$movesetRatedPokemonExists) {
				$this->movesetRatedPokemonRepository->insert(
					$year,
					$month,
					$formatId,
					$rating,
					$pokemonId,
					$averageWeight
				);
			}

			// If the last line was viability ceiling, read and ignore the next line.
			if ($viabilityCeiling !== null) {
				\GuzzleHttp\Psr7\readline($stream); // Separator.
			}

			// BLOCK 3 - Abilities.

			\GuzzleHttp\Psr7\readline($stream); // "Abilities"
			while ($this->movesetFileExtractor->isNamePercent($line = \GuzzleHttp\Psr7\readline($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);
				$showdownAbilityName = $namePercent->showdownName();

				// If this ability is not meant to be imported, skip it.
				if (!$this->showdownAbilityRepository->isImported($showdownAbilityName)) {
					continue;
				}

				$abilityId = $this->showdownAbilityRepository->getAbilityId($showdownAbilityName);

				if ($isPokemonImported && !$movesetRatedPokemonExists) {
					$this->movesetRatedAbilitiesRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$abilityId,
						$namePercent->percent()
					);
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

				// If this item is not meant to be imported, skip it.
				if (!$this->showdownItemRepository->isImported($showdownItemName)) {
					continue;
				}

				$itemId = $this->showdownItemRepository->getItemId($showdownItemName);

				if ($isPokemonImported && !$movesetRatedPokemonExists) {
					$this->movesetRatedItemsRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$itemId,
						$namePercent->percent()
					);
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

				$natureId = $this->showdownNatureRepository->getNatureId($showdownNatureName);

				if ($isPokemonImported && !$movesetRatedPokemonExists) {
					$this->movesetRatedSpreadsRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$natureId,
						$spread->hp(),
						$spread->atk(),
						$spread->def(),
						$spread->spa(),
						$spread->spd(),
						$spread->spe(),
						$spread->percent()
					);
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

				// If this move is not meant to be imported, skip it.
				if (!$this->showdownMoveRepository->isImported($showdownMoveName)) {
					continue;
				}

				$moveId = $this->showdownMoveRepository->getMoveId($showdownMoveName);

				if ($isPokemonImported && !$movesetRatedPokemonExists) {
					$this->movesetRatedMovesRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$moveId,
						$namePercent->percent()
					);
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

				// If this Pokémon is not meant to be imported, skip it.
				if (!$this->showdownPokemonRepository->isImported($showdownTeammateName)) {
					continue;
				}

				$teammateId = $this->showdownPokemonRepository->getPokemonId($showdownTeammateName);

				if ($isPokemonImported && !$movesetRatedPokemonExists) {
					$this->movesetRatedTeammatesRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$teammateId,
						$namePercent->percent()
					);
				}

				// If the file randomly ends here, there's nothing else to do.
				if ($stream->eof()) {
					return;
				}
			}

			// BLOCK 8 - Counters

			\GuzzleHttp\Psr7\readline($stream); // "Counters"
			while ($this->movesetFileExtractor->isCounter1($line1 = \GuzzleHttp\Psr7\readline($stream))) {
				$line2 = \GuzzleHttp\Psr7\readline($stream);
				$counter = $this->movesetFileExtractor->extractCounter($line1, $line2);
				$showdownCounterName = $counter->showdownPokemonName();

				// If this Pokémon is not meant to be imported, skip it.
				if (!$this->showdownPokemonRepository->isImported($showdownCounterName)) {
					continue;
				}

				$counterId = $this->showdownPokemonRepository->getPokemonId($showdownCounterName);

				if ($isPokemonImported && !$movesetRatedPokemonExists) {
					$this->movesetRatedCountersRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$counterId,
						$counter->number1(),
						$counter->number2(),
						$counter->number3(),
						$counter->percentKnockedOut(),
						$counter->percentSwitchedOut()
					);
				}
			}
		}
	}
}
