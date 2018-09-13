<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MoveUsageMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveDescription;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveName;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;

class MoveUsageMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var MoveDescriptionRepositoryInterface $moveDescriptionRepository */
	private $moveDescriptionRepository;

	/** @var UsageRatedRepositoryInterface $usageRatedRepository */
	private $usageRatedRepository;

	/** @var UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository */
	private $usageRatedPokemonMoveRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


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

	/** @var string $moveIdentifier */
	private $moveIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var MoveName $moveName */
	private $moveName;

	/** @var MoveDescription $moveDescription */
	private $moveDescription;

	/** @var MoveUsageData[] $moveUsageDatas */
	private $moveUsageDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param MoveDescriptionRepositoryInterface $moveDescriptionRepository
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
		MoveNameRepositoryInterface $moveNameRepository,
		MoveDescriptionRepositoryInterface $moveDescriptionRepository,
		UsageRatedRepositoryInterface $usageRatedRepository,
		UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->moveRepository = $moveRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->moveDescriptionRepository = $moveDescriptionRepository;
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
	 * @param string $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $moveIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $moveIdentifier,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->moveIdentifier = $moveIdentifier;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Does usage rated data exist for the previous month?
		$this->prevMonthDataExists = $this->usageRatedRepository->has(
			$prevMonth,
			$format->getId(),
			$rating
		);

		// Does usage rated data exist for the next month?
		$this->nextMonthDataExists = $this->usageRatedRepository->has(
			$nextMonth,
			$format->getId(),
			$rating
		);

		// Get the move.
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		// Get the move name.
		$this->moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$move->getId()
		);

		// Get the move description.
		$this->moveDescription = $this->moveDescriptionRepository->getByGenerationAndLanguageAndMove(
			$format->getGeneration(),
			$languageId,
			$move->getId()
		);

		// Get usage rated Pokémon move records for this month.
		$usageRatedPokemonMoves = $this->usageRatedPokemonMoveRepository->getByMonthAndFormatAndRatingAndMove(
			$thisMonth,
			$format->getId(),
			$rating,
			$move->getId()
		);

		// Get usage rated Pokémon move records for the previous month.
		$prevMonthPokemonMoves = $this->usageRatedPokemonMoveRepository->getByMonthAndFormatAndRatingAndMove(
			$prevMonth,
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

			// Get this usage rated Pokémon move's change in usage percent from
			// the previous month.
			$prevMonthUsagePercent = 0;
			if (isset($prevMonthPokemonMoves[$pokemonId->value()])) {
				$prevMonthUsagePercent = $prevMonthPokemonMoves[$pokemonId->value()]->getUsagePercent();
			}
			$change = $usageRatedPokemonMove->getUsagePercent() - $prevMonthUsagePercent;

			$this->moveUsageDatas[] = new MoveUsageData(
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$usageRatedPokemonMove->getPokemonPercent(),
				$usageRatedPokemonMove->getMovePercent(),
				$usageRatedPokemonMove->getUsagePercent(),
				$change
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
	 * Get the move identifier.
	 *
	 * @return string
	 */
	public function getMoveIdentifier() : string
	{
		return $this->moveIdentifier;
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
	 * Get the move name.
	 *
	 * @return MoveName
	 */
	public function getMoveName() : MoveName
	{
		return $this->moveName;
	}

	/**
	 * Get the move description.
	 *
	 * @return MoveDescription
	 */
	public function getMoveDescription() : MoveDescription
	{
		return $this->moveDescription;
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
