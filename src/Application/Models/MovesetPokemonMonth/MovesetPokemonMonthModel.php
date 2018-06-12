<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;

class MovesetPokemonMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var MovesetPokemonRepositoryInterface $movesetPokemonRepository */
	private $movesetPokemonRepository;

	/** @var MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository */
	private $movesetRatedPokemonRepository;

	/** @var PokemonModel $pokemonModel */
	private $pokemonModel;

	/** @var AbilityModel $abilityModel */
	private $abilityModel;

	/** @var ItemModel $itemModel */
	private $itemModel;

	/** @var SpreadModel $spreadModel */
	private $spreadModel;

	/** @var MoveModel $moveModel */
	private $moveModel;

	/** @var TeammateModel $teammateModel */
	private $teammateModel;

	/** @var CounterModel $counterModel */
	private $counterModel;


	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var string $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;


	/** @var MovesetPokemon $movesetPokemon */
	private $movesetPokemon;

	/** @var MovesetRatedPokemon $movesetRatedPokemon */
	private $movesetRatedPokemon;

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param MovesetPokemonRepositoryInterface $movesetPokemonRepository
	 * @param MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository
	 * @param PokemonModel $pokemonModel
	 * @param AbilityModel $abilityModel
	 * @param ItemModel $itemModel
	 * @param SpreadModel $spreadModel
	 * @param MoveModel $moveModel
	 * @param TeammateModel $teammateModel
	 * @param CounterModel $counterModel
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		MovesetPokemonRepositoryInterface $movesetPokemonRepository,
		MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		PokemonModel $pokemonModel,
		AbilityModel $abilityModel,
		ItemModel $itemModel,
		SpreadModel $spreadModel,
		MoveModel $moveModel,
		TeammateModel $teammateModel,
		CounterModel $counterModel
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->movesetPokemonRepository = $movesetPokemonRepository;
		$this->movesetRatedPokemonRepository = $movesetRatedPokemonRepository;
		$this->pokemonModel = $pokemonModel;
		$this->abilityModel = $abilityModel;
		$this->itemModel = $itemModel;
		$this->spreadModel = $spreadModel;
		$this->moveModel = $moveModel;
		$this->teammateModel = $teammateModel;
		$this->counterModel = $counterModel;
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
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get the Pokémon.
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Does moveset rated Pokémon data exist for the previous month?
		$this->prevMonthDataExists = $this->movesetRatedPokemonRepository->has(
			$prevMonth,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Does moveset rated Pokémon data exist for the next month?
		$this->nextMonthDataExists = $this->movesetRatedPokemonRepository->has(
			$nextMonth,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get the moveset Pokémon record.
		$this->movesetPokemon = $this->movesetPokemonRepository->getByMonthAndFormatAndPokemon(
			$thisMonth,
			$format->getId(),
			$pokemon->getId()
		);

		// Get moveset rated Pokémon record.
		$this->movesetRatedPokemon = $this->movesetRatedPokemonRepository->getByMonthAndFormatAndRatingAndPokemon(
			$thisMonth,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get Pokémon data.
		$this->pokemonModel->setData(
			$format->getGeneration(),
			$pokemon->getId(),
			$languageId
		);

		// Get ability data.
		$this->abilityModel->setData(
			$thisMonth,
			$prevMonth,
			$format->getId(),
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get item data.
		$this->itemModel->setData(
			$thisMonth,
			$prevMonth,
			$format->getId(),
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get spread data.
		$this->spreadModel->setData(
			$thisMonth,
			$format->getId(),
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get move data.
		$this->moveModel->setData(
			$thisMonth,
			$prevMonth,
			$format->getId(),
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get teammate data.
		$this->teammateModel->setData(
			$thisMonth,
			$format,
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get counter data.
		$this->counterModel->setData(
			$thisMonth,
			$format,
			$rating,
			$pokemon->getId(),
			$languageId
		);
	}

	/**
	 * Does usage rated data exist for the previous month?
	 *
	 * @return bool
	 */
	public function doesPrevMonthDataExist() : bool
	{
		return $this->prevMonthDataExists;
	}

	/**
	 * Does usage rated data exist for the next month?
	 *
	 * @return bool
	 */
	public function doesNextMonthDataExist() : bool
	{
		return $this->nextMonthDataExists;
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
	 * Get the month.
	 *
	 * @return string
	 */
	public function getMonth() : string
	{
		return $this->month;
	}

	/**
	 * Get the format identifier.
	 *
	 * @return string
	 */
	public function getFormatIdentifier() : string
	{
		return $this->formatIdentifier;
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
	 * Get the Pokémon identifier.
	 *
	 * @return string
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
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
	 * Get the Pokémon model.
	 *
	 * @return PokemonModel
	 */
	public function getPokemonModel() : PokemonModel
	{
		return $this->pokemonModel;
	}

	/**
	 * Get the moveset Pokémon record.
	 *
	 * @return MovesetPokemon
	 */
	public function getMovesetPokemon() : MovesetPokemon
	{
		return $this->movesetPokemon;
	}

	/**
	 * Get the moveset rated Pokémon record.
	 *
	 * @return MovesetRatedPokemon
	 */
	public function getMovesetRatedPokemon() : MovesetRatedPokemon
	{
		return $this->movesetRatedPokemon;
	}

	/**
	 * Get the ability datas.
	 *
	 * @return AbilityData[]
	 */
	public function getAbilityDatas() : array
	{
		return $this->abilityModel->getAbilityDatas();
	}

	/**
	 * Get the the item datas.
	 *
	 * @return ItemData[]
	 */
	public function getItemDatas() : array
	{
		return $this->itemModel->getItemDatas();
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
	 * Get the move datas.
	 *
	 * @return MoveData[]
	 */
	public function getMoveDatas() : array
	{
		return $this->moveModel->getMoveDatas();
	}

	/**
	 * Get the teammate datas.
	 *
	 * @return TeammateData[]
	 */
	public function getTeammateDatas() : array
	{
		return $this->teammateModel->getTeammateDatas();
	}

	/**
	 * Get the counter datas.
	 *
	 * @return CounterData[]
	 */
	public function getCounterDatas() : array
	{
		return $this->counterModel->getCounterDatas();
	}
}
