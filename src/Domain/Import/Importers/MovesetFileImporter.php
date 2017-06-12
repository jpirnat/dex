<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Import\Extractors\MovesetFileExtractor;
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
use Jp\Dex\Domain\Stats\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Psr\Http\Message\StreamInterface;

class MovesetFileImporter
{
	/** @var ShowdownPokemonRepositoryInterface $showdownPokemonRepository */
	private $showdownPokemonRepository;

	/** @var ShowdownAbilityRepositoryInterface $showdownAbilityRepository */
	private $showdownAbilityRepository;

	/** @var ShowdownItemRepositoryInterface $showdownItemRepository */
	private $showdownItemRepository;

	/** @var ShowdownNatureRepositoryInterface $showdownNatureRepository */
	private $showdownNatureRepository;

	/** @var ShowdownMoveRepositoryInterface $showdownMoveRepository */
	private $showdownMoveRepository;

	/** @var MovesetPokemonRepositoryInterface $movesetPokemonRepository */
	private $movesetPokemonRepository;

	/** @var MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository */
	private $movesetRatedPokemonRepository;

	/** @var MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository */
	private $movesetRatedAbilityRepository;

	/** @var MovesetRatedItemRepositoryInterface $movesetRatedItemRepository */
	private $movesetRatedItemRepository;

	/** @var MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository */
	private $movesetRatedSpreadRepository;

	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	private $movesetRatedMoveRepository;

	/** @var MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository */
	private $movesetRatedTeammateRepository;

	/** @var MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository */
	private $movesetRatedCounterRepository;

	/** @var MovesetFileExtractor $movesetFileExtractor */
	private $movesetFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepositoryInterface $showdownPokemonRepository
	 * @param ShowdownAbilityRepositoryInterface $showdownAbilityRepository
	 * @param ShowdownItemRepositoryInterface $showdownItemRepository
	 * @param ShowdownNatureRepositoryInterface $showdownNatureRepository
	 * @param ShowdownMoveRepositoryInterface $showdownMoveRepository
	 * @param MovesetPokemonRepositoryInterface $movesetPokemonRepository
	 * @param MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository
	 * @param MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	 * @param MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	 * @param MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 * @param MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository
	 * @param MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository
	 * @param MovesetFileExtractor $movesetFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
		ShowdownItemRepositoryInterface $showdownItemRepository,
		ShowdownNatureRepositoryInterface $showdownNatureRepository,
		ShowdownMoveRepositoryInterface $showdownMoveRepository,
		MovesetPokemonRepositoryInterface $movesetPokemonRepository,
		MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository,
		MovesetRatedItemRepositoryInterface $movesetRatedItemRepository,
		MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository,
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository,
		MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository,
		MovesetFileExtractor $movesetFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->showdownAbilityRepository = $showdownAbilityRepository;
		$this->showdownItemRepository = $showdownItemRepository;
		$this->showdownNatureRepository = $showdownNatureRepository;
		$this->showdownMoveRepository = $showdownMoveRepository;
		$this->movesetPokemonRepository = $movesetPokemonRepository;
		$this->movesetRatedPokemonRepository = $movesetRatedPokemonRepository;
		$this->movesetRatedAbilityRepository = $movesetRatedAbilityRepository;
		$this->movesetRatedItemRepository = $movesetRatedItemRepository;
		$this->movesetRatedSpreadRepository = $movesetRatedSpreadRepository;
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
		$this->movesetRatedTeammateRepository = $movesetRatedTeammateRepository;
		$this->movesetRatedCounterRepository = $movesetRatedCounterRepository;
		$this->movesetFileExtractor = $movesetFileExtractor;
	}

	/**
	 * Import moveset data from the given file.
	 *
	 * @param StreamInterface $stream
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function import(
		StreamInterface $stream,
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : void {
		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$movesetPokemonExists = $this->movesetPokemonRepository->has(
			$year,
			$month,
			$formatId
		);
		$movesetRatedPokemonExists = $this->movesetRatedPokemonRepository->has(
			$year,
			$month,
			$formatId,
			$rating
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
				$pokemonId = 0;
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
				$movesetPokemon = new MovesetPokemon(
					$year,
					$month,
					$formatId,
					$pokemonId,
					$rawCount,
					$viabilityCeiling
				);

				$this->movesetPokemonRepository->save($movesetPokemon);
			}

			if ($isPokemonImported && !$movesetRatedPokemonExists) {
				$movesetRatedPokemon = new MovesetRatedPokemon(
					$year,
					$month,
					$formatId,
					$rating,
					$pokemonId,
					$averageWeight
				);

				$this->movesetRatedPokemonRepository->save($movesetRatedPokemon);
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
					$movesetRatedAbility = new MovesetRatedAbility(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$abilityId,
						$namePercent->percent()
					);

					$this->movesetRatedAbilityRepository->save($movesetRatedAbility);
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
					$movesetRatedItem = new MovesetRatedItem(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$itemId,
						$namePercent->percent()
					);

					$this->movesetRatedItemRepository->save($movesetRatedItem);
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
					$evSpread = new StatValueContainer();
					$evSpread->add(new StatValue(new StatId(StatId::HP), $spread->hp()));
					$evSpread->add(new StatValue(new StatId(StatId::ATTACK), $spread->atk()));
					$evSpread->add(new StatValue(new StatId(StatId::DEFENSE), $spread->def()));
					$evSpread->add(new StatValue(new StatId(StatId::SPECIAL_ATTACK), $spread->spa()));
					$evSpread->add(new StatValue(new StatId(StatId::SPECIAL_DEFENSE), $spread->spd()));
					$evSpread->add(new StatValue(new StatId(StatId::SPEED), $spread->spe()));

					$movesetRatedSpread = new MovesetRatedSpread(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$natureId,
						$evSpread,
						$spread->percent()
					);

					$this->movesetRatedSpreadRepository->save($movesetRatedSpread);
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
					$movesetRatedMove = new MovesetRatedMove(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$moveId,
						$namePercent->percent()
					);

					$this->movesetRatedMoveRepository->save($movesetRatedMove);
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
					$movesetRatedTeammate = new MovesetRatedTeammate(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$teammateId,
						$namePercent->percent()
					);

					$this->movesetRatedTeammateRepository->save($movesetRatedTeammate);
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
					$movesetRatedCounter = new MovesetRatedCounter(
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

					$this->movesetRatedCounterRepository->save($movesetRatedCounter);
				}
			}
		}
	}
}
