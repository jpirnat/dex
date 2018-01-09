<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MoveUsageMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;

class MoveUsageMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var UsageRatedRepositoryInterface $usageRatedRepository */
	private $usageRatedRepository;

	/** @var UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository */
	private $usageRatedPokemonMoveRepository;

	/** PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $moveIdentifier */
	private $moveIdentifier;

	/** @var MoveUsageData[] $moveUsageDatas */
	private $moveUsageDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param MoveRepositoryInterface $moveRepository
	 * @param UsageRatedRepositoryInterface $usageRatedRepository
	 * @param UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		MoveRepositoryInterface $moveRepository,
		UsageRatedRepositoryInterface $usageRatedRepository,
		UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->moveRepository = $moveRepository;
		$this->usageRatedRepository = $usageRatedRepository;
		$this->usageRatedPokemonMoveRepository = $usageRatedPokemonMoveRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $moveIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		string $formatIdentifier,
		int $rating,
		string $moveIdentifier,
		LanguageId $languageId
	) : void {
		$this->year = $year;
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->moveIdentifier = $moveIdentifier;

		// Get the previous month and the next month.
		$this->dateModel->setData($year, $month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Does usage rated data exist for the previous month?
		$this->prevMonthDataExists = $this->usageRatedRepository->has(
			$prevMonth->getYear(),
			$prevMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Does usage rated data exist for the next month?
		$this->nextMonthDataExists = $this->usageRatedRepository->has(
			$nextMonth->getYear(),
			$nextMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Get the move.
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		// Get usage rated Pokémon move records for this month.
		$usageRatedPokemonMoves = $this->usageRatedPokemonMoveRepository->getByYearAndMonthAndFormatAndRatingAndMove(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId(),
			$rating,
			$move->getId()
		);

		// Get each usage record's data.
		foreach ($usageRatedPokemonMoves as $usageRatedPokemonMove) {
			$pokemonId = $usageRatedPokemonMove->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$pokemonId
			);

			// Get this Pokémon.
			$pokemon = $this->pokemonRepository->getById($pokemonId);

			// Get this Pokémon's form icon.
			$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
				$format->getGeneration(),
				new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
				false,
				false
			);

			$this->moveUsageDatas[] = new MoveUsageData(
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$usageRatedPokemonMove->getPokemonPercent(),
				$usageRatedPokemonMove->getMovePercent(),
				$usageRatedPokemonMove->getUsagePercent()
			);
		}
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
	 * Get the year.
	 *
	 * @return int
	 */
	public function getYear() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function getMonth() : int
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
	 * Get the move identifier.
	 *
	 * @return string
	 */
	public function getMoveIdentifier() : string
	{
		return $this->moveIdentifier;
	}

	/**
	 * Get the move usage datas.
	 *
	 * @return MoveUsageData[]
	 */
	public function getMoveUsageDatas() : array
	{
		return $this->moveUsageDatas;
	}
}
