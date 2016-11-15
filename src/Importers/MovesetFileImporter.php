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

		// TODO
	}
}
