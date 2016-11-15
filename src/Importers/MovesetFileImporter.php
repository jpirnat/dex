<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers;

use Exception;
use Jp\Trendalyzer\Importers\Extractors\MovesetFileExtractor;
use Jp\Trendalyzer\Repositories\AbilitiesRepository;
use Jp\Trendalyzer\Repositories\ItemsRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetPokemonRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedAbilitiesRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedCountersRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedItemsRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedMovesRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedPokemonRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedSpreadsRepository;
use Jp\Trendalyzer\Repositories\Moveset\MovesetRatedTeammatesRepository;
use Jp\Trendalyzer\Repositories\MovesRepository;
use Jp\Trendalyzer\Repositories\NaturesRepository;
use Jp\Trendalyzer\Repositories\PokemonRepository;
use Psr\Http\Message\StreamInterface;

class MovesetFileImporter
{
	/** @var PokemonRepository $pokemonRepository */
	protected $pokemonRepository;

	/** @var AbilitiesRepository $abilitiesRepository */
	protected $abilitiesRepository;

	/** @var ItemsRepository $itemsRepository */
	protected $itemsRepository;

	/** @var NaturesRepository $naturesRepository */
	protected $naturesRepository;

	/** @var MovesRepository $movesRepository */
	protected $movesRepository;

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
	 * @param PokemonRepository $pokemonRepository
	 * @param AbilitiesRepository $abilitiesRepository
	 * @param ItemsRepository $itemsRepository
	 * @param NaturesRepository $naturesRepository
	 * @param MovesRepository $movesRepository
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
		PokemonRepository $pokemonRepository,
		AbilitiesRepository $abilitiesRepository,
		ItemsRepository $itemsRepository,
		NaturesRepository $naturesRepository,
		MovesRepository $movesRepository,
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
		$this->pokemonRepository = $pokemonRepository;
		$this->abilitiesRepository = $abilitiesRepository;
		$this->itemsRepository = $itemsRepository;
		$this->naturesRepository = $naturesRepository;
		$this->movesRepository = $movesRepository;
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
	) {
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
				break;
			}
			$pokemonName = $this->movesetFileExtractor->extractPokemonName($line);
			$pokemonId = $this->pokemonRepository->getPokemonId($pokemonName);
			\GuzzleHttp\Psr7\readline($stream); // Separator.

			// BLOCK 2 - General information.

			$line = \GuzzleHttp\Psr7\readline($stream); // Raw count.
			$rawCount = $this->movesetFileExtractor->extractRawCount($line);

			$line = \GuzzleHttp\Psr7\readline($stream); // Average weight.
			$averageWeight = $this->movesetFileExtractor->extractAverageWeight($line);

			$line = \GuzzleHttp\Psr7\readline($stream); // Viability ceiling OR separator.
			try {
				$viabilityCeiling = $this->movesetFileExtractor->extractViabilityCeiling($line);
			} catch (Exception $e) {
				$viabilityCeiling = 0;
			}

			if (!$movesetPokemonExists) {
				$this->movesetPokemonRepository->insert(
					$year,
					$month,
					$formatId,
					$pokemonId,
					$rawCount,
					$viabilityCeiling
				);
			}

			if (!$movesetRatedPokemonExists) {
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
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				// Ignore this line if it's an "Other" percent.
				if ($this->movesetFileExtractor->isOther($line)) {
					continue;
				}

				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);

				$abilityName = $namePercent->name();
				$abilityId = $this->abilitiesRepository->getAbilityId($abilityName);

				if (!$movesetRatedPokemonExists) {
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
			}

			// BLOCK 4 - Items.

			\GuzzleHttp\Psr7\readline($stream); // "Items"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				// Ignore this line if it's an "Other" percent.
				if ($this->movesetFileExtractor->isOther($line)) {
					continue;
				}

				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);

				$itemName = $namePercent->name();
				$itemId = $this->itemsRepository->getItemId($itemName);

				if (!$movesetRatedPokemonExists) {
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
			}

			// BLOCK 5 - Spreads.

			\GuzzleHttp\Psr7\readline($stream); // "Spreads"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				// Ignore this line if it's an "Other" percent.
				if ($this->movesetFileExtractor->isOther($line)) {
					continue;
				}

				$spread = $this->movesetFileExtractor->extractSpread($line);

				$natureName = $spread->natureName();
				$natureId = $this->naturesRepository->getNatureId($natureName);

				if (!$movesetRatedPokemonExists) {
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
			}

			// BLOCK 6 - Moves.

			\GuzzleHttp\Psr7\readline($stream); // "Moves"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				// Ignore this line if it's an "Other" percent.
				if ($this->movesetFileExtractor->isOther($line)) {
					continue;
				}

				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);

				$moveName = $namePercent->name();
				$moveId = $this->movesRepository->getMoveId($moveName);

				if (!$movesetRatedPokemonExists) {
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
			}

			// BLOCK 7 - Teammates.

			\GuzzleHttp\Psr7\readline($stream); // "Teammates"
			while (!$this->movesetFileExtractor->isSeparator($line = \GuzzleHttp\Psr7\readline($stream))) {
				$namePercent = $this->movesetFileExtractor->extractNamePercent($line);

				$teammateName = $namePercent->name();
				$teammateId = $this->pokemonRepository->getPokemonId($teammateName);

				if (!$movesetRatedPokemonExists) {
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
			}

			// BLOCK 8 - Counters

			\GuzzleHttp\Psr7\readline($stream); // "Teammates"
			while (!$this->movesetFileExtractor->isSeparator($line1 = \GuzzleHttp\Psr7\readline($stream))) {
				$line2 = \GuzzleHttp\Psr7\readline($stream);
				$counter = $this->movesetFileExtractor->extractCounter($line1, $line2);

				$counterName = $counter->pokemonName();
				$counterId = $this->pokemonRepository->getPokemonId($counterName);

				if (!$movesetRatedPokemonExists) {
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
