<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Abilities\StatsPokemonAbility;
use Jp\Dex\Domain\Abilities\StatsPokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Counters\StatsPokemonCounter;
use Jp\Dex\Domain\Counters\StatsPokemonCounterRepositoryInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\StatsPokemonItem;
use Jp\Dex\Domain\Items\StatsPokemonItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\StatsPokemonMove;
use Jp\Dex\Domain\Moves\StatsPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Teammates\StatsPokemonTeammate;
use Jp\Dex\Domain\Teammates\StatsPokemonTeammateRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;

class StatsPokemonModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var GenerationRepositoryInterface $generationRepository */
	private $generationRepository;

	/** @var MovesetPokemonRepositoryInterface $movesetPokemonRepository */
	private $movesetPokemonRepository;

	/** @var MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository */
	private $movesetRatedPokemonRepository;


	/** @var PokemonModel $pokemonModel */
	private $pokemonModel;

	/** @var StatsPokemonAbilityRepositoryInterface $statsPokemonAbilityRepository */
	private $statsPokemonAbilityRepository;

	/** @var StatsPokemonItemRepositoryInterface $statsPokemonItemRepository */
	private $statsPokemonItemRepository;

	/** @var SpreadModel $spreadModel */
	private $spreadModel;

	/** @var StatsPokemonMoveRepositoryInterface $statsPokemonMoveRepository */
	private $statsPokemonMoveRepository;

	/** @var StatsPokemonTeammateRepositoryInterface $statsPokemonTeammateRepository */
	private $statsPokemonTeammateRepository;

	/** @var StatsPokemonCounterRepositoryInterface $statsPokemonCounterRepository */
	private $statsPokemonCounterRepository;


	/** @var string $month */
	private $month;

	/** @var Format $format */
	private $format;

	/** @var int $rating */
	private $rating;

	/** @var Pokemon $pokemon */
	private $pokemon;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var MovesetPokemon|null $movesetPokemon */
	private $movesetPokemon;

	/** @var MovesetRatedPokemon|null $movesetRatedPokemon */
	private $movesetRatedPokemon;

	/** @var Generation $generation */
	private $generation;

	/** @var StatsPokemonAbility[] $abilities */
	private $abilities = [];

	/** @var StatsPokemonItem[] $items */
	private $items = [];

	/** @var StatsPokemonMove[] $moves */
	private $moves;

	/** @var StatsPokemonTeammate[] $teammates */
	private $teammates = [];

	/** @var StatsPokemonCounter[] $counters */
	private $counters = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param RatingQueriesInterface $ratingQueries
	 * @param GenerationRepositoryInterface $generationRepository
	 * @param MovesetPokemonRepositoryInterface $movesetPokemonRepository
	 * @param MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository
	 * @param PokemonModel $pokemonModel
	 * @param StatsPokemonAbilityRepositoryInterface $statsPokemonAbilityRepository
	 * @param StatsPokemonItemRepositoryInterface $statsPokemonItemRepository
	 * @param SpreadModel $spreadModel
	 * @param StatsPokemonMoveRepositoryInterface $statsPokemonMoveRepository
	 * @param StatsPokemonTeammateRepositoryInterface $statsPokemonTeammateRepository
	 * @param StatsPokemonCounterRepositoryInterface $statsPokemonCounterRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		RatingQueriesInterface $ratingQueries,
		GenerationRepositoryInterface $generationRepository,
		MovesetPokemonRepositoryInterface $movesetPokemonRepository,
		MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		PokemonModel $pokemonModel,
		StatsPokemonAbilityRepositoryInterface $statsPokemonAbilityRepository,
		StatsPokemonItemRepositoryInterface $statsPokemonItemRepository,
		SpreadModel $spreadModel,
		StatsPokemonMoveRepositoryInterface $statsPokemonMoveRepository,
		StatsPokemonTeammateRepositoryInterface $statsPokemonTeammateRepository,
		StatsPokemonCounterRepositoryInterface $statsPokemonCounterRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->ratingQueries = $ratingQueries;
		$this->generationRepository = $generationRepository;
		$this->movesetPokemonRepository = $movesetPokemonRepository;
		$this->movesetRatedPokemonRepository = $movesetRatedPokemonRepository;
		$this->pokemonModel = $pokemonModel;
		$this->statsPokemonAbilityRepository = $statsPokemonAbilityRepository;
		$this->statsPokemonItemRepository = $statsPokemonItemRepository;
		$this->spreadModel = $spreadModel;
		$this->statsPokemonMoveRepository = $statsPokemonMoveRepository;
		$this->statsPokemonTeammateRepository = $statsPokemonTeammateRepository;
		$this->statsPokemonCounterRepository = $statsPokemonCounterRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 *
	 * @param string $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $pokemonIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

		// Get the Pokémon.
		$this->pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId()
		);

		// Get Pokémon data.
		$this->pokemonModel->setData(
			$this->format->getGenerationId(),
			$this->pokemon->getId(),
			$languageId
		);

		// Get the format's generation.
		$this->generation = $this->generationRepository->getById(
			$this->format->getGenerationId()
		);

		// Get the moveset Pokémon record.
		$this->movesetPokemon = $this->movesetPokemonRepository->getByMonthAndFormatAndPokemon(
			$thisMonth,
			$this->format->getId(),
			$this->pokemon->getId()
		);

		// Get moveset rated Pokémon record.
		$this->movesetRatedPokemon = $this->movesetRatedPokemonRepository->getByMonthAndFormatAndRatingAndPokemon(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId()
		);

		// Get ability data.
		$this->abilities = $this->statsPokemonAbilityRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);

		// Get item data.
		$this->items = $this->statsPokemonItemRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);

		// Get spread data.
		$this->spreadModel->setData(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);

		// Get move data.
		$this->moves = $this->statsPokemonMoveRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);

		// Get teammate data.
		$this->teammates = $this->statsPokemonTeammateRepository->getByMonth(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getGenerationId(),
			$languageId
		);

		// Get counter data.
		$this->counters = $this->statsPokemonCounterRepository->getByMonth(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getGenerationId(),
			$languageId
		);
	}

	/**
	 * Get the month.
	 *
	 * @return string
	 */
	public function getMonth() : string
	{
		return $this->month;
	}

	/**
	 * Get the format.
	 *
	 * @return Format
	 */
	public function getFormat() : Format
	{
		return $this->format;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return Pokemon
	 */
	public function getPokemon() : Pokemon
	{
		return $this->pokemon;
	}

	/**
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the date model.
	 *
	 * @return DateModel
	 */
	public function getDateModel() : DateModel
	{
		return $this->dateModel;
	}

	/**
	 * Get the ratings for this month.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}

	/**
	 * Get the Pokémon model.
	 *
	 * @return PokemonModel
	 */
	public function getPokemonModel() : PokemonModel
	{
		return $this->pokemonModel;
	}

	/**
	 * Get the generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the moveset Pokémon record.
	 *
	 * @return MovesetPokemon|null
	 */
	public function getMovesetPokemon() : ?MovesetPokemon
	{
		return $this->movesetPokemon;
	}

	/**
	 * Get the moveset rated Pokémon record.
	 *
	 * @return MovesetRatedPokemon|null
	 */
	public function getMovesetRatedPokemon() : ?MovesetRatedPokemon
	{
		return $this->movesetRatedPokemon;
	}

	/**
	 * Get the abilities.
	 *
	 * @return StatsPokemonAbility[]
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * Get the items.
	 *
	 * @return StatsPokemonItem[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}

	/**
	 * Get the spread datas.
	 *
	 * @return SpreadData[]
	 */
	public function getSpreadDatas() : array
	{
		return $this->spreadModel->getSpreadDatas();
	}

	/**
	 * Get the moves.
	 *
	 * @return StatsPokemonMove[]
	 */
	public function getMoves() : array
	{
		return $this->moves;
	}

	/**
	 * Get the teammates.
	 *
	 * @return StatsPokemonTeammate[]
	 */
	public function getTeammates() : array
	{
		return $this->teammates;
	}

	/**
	 * Get the counters.
	 *
	 * @return StatsPokemonCounter[]
	 */
	public function getCounters() : array
	{
		return $this->counters;
	}
}
