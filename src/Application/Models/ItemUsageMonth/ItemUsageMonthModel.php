<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\ItemUsageMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemDescription;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;

class ItemUsageMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	private $itemRepository;

	/** @var MonthQueriesInterface $monthQueries */
	private $monthQueries;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var ItemDescriptionRepositoryInterface $itemDescriptionRepository */
	private $itemDescriptionRepository;

	/** @var UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository */
	private $usageRatedPokemonItemRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


	/** @var string $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $itemIdentifier */
	private $itemIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var bool $prevMonthDataExists */
	private $prevMonthDataExists;

	/** @var bool $nextMonthDataExists */
	private $nextMonthDataExists;

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var ItemName $itemName */
	private $itemName;

	/** @var ItemDescription $itemDescription */
	private $itemDescription;

	/** @var ItemUsageData[] $itemUsageDatas */
	private $itemUsageDatas = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param ItemRepositoryInterface $itemRepository
	 * @param MonthQueriesInterface $monthQueries
	 * @param RatingQueriesInterface $ratingQueries
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param ItemDescriptionRepositoryInterface $itemDescriptionRepository
	 * @param UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		ItemRepositoryInterface $itemRepository,
		MonthQueriesInterface $monthQueries,
		RatingQueriesInterface $ratingQueries,
		ItemNameRepositoryInterface $itemNameRepository,
		ItemDescriptionRepositoryInterface $itemDescriptionRepository,
		UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->itemRepository = $itemRepository;
		$this->monthQueries = $monthQueries;
		$this->ratingQueries = $ratingQueries;
		$this->itemNameRepository = $itemNameRepository;
		$this->itemDescriptionRepository = $itemDescriptionRepository;
		$this->usageRatedPokemonItemRepository = $usageRatedPokemonItemRepository;
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
	 * @param string $itemIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $itemIdentifier,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->itemIdentifier = $itemIdentifier;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get the item.
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		// Does usage data exist for the previous month?
		$this->prevMonthDataExists = $this->monthQueries->doesMonthFormatDataExist(
			$prevMonth,
			$format->getId()
		);

		// Does usage data exist for the next month?
		$this->nextMonthDataExists = $this->monthQueries->doesMonthFormatDataExist(
			$nextMonth,
			$format->getId()
		);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$format->getId()
		);

		// Get the item name.
		$this->itemName = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			$item->getId()
		);

		// Get the item description.
		$this->itemDescription = $this->itemDescriptionRepository->getByGenerationAndLanguageAndItem(
			$format->getGenerationId(),
			$languageId,
			$item->getId()
		);

		// Get usage rated Pokémon item records for this month.
		$usageRatedPokemonItems = $this->usageRatedPokemonItemRepository->getByMonthAndFormatAndRatingAndItem(
			$thisMonth,
			$format->getId(),
			$rating,
			$item->getId()
		);

		// Get usage rated Pokémon item records for the previous month.
		$prevMonthPokemonItems = $this->usageRatedPokemonItemRepository->getByMonthAndFormatAndRatingAndItem(
			$prevMonth,
			$format->getId(),
			$rating,
			$item->getId()
		);

		// Get each usage record's data.
		foreach ($usageRatedPokemonItems as $usageRatedPokemonItem) {
			$pokemonId = $usageRatedPokemonItem->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$pokemonId
			);

			// Get this Pokémon.
			$pokemon = $this->pokemonRepository->getById($pokemonId);

			// Get this Pokémon's form icon.
			$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
				$format->getGenerationId(),
				new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
				false,
				false
			);

			// Get this usage rated Pokémon item's change in usage percent from
			// the previous month.
			$prevMonthUsagePercent = 0;
			if (isset($prevMonthPokemonItems[$pokemonId->value()])) {
				$prevMonthUsagePercent = $prevMonthPokemonItems[$pokemonId->value()]->getUsagePercent();
			}
			$change = $usageRatedPokemonItem->getUsagePercent() - $prevMonthUsagePercent;

			$this->itemUsageDatas[] = new ItemUsageData(
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$usageRatedPokemonItem->getPokemonPercent(),
				$usageRatedPokemonItem->getItemPercent(),
				$usageRatedPokemonItem->getUsagePercent(),
				$change
			);
		}
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
	 * Get the item identifier.
	 *
	 * @return string
	 */
	public function getItemIdentifier() : string
	{
		return $this->itemIdentifier;
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
	 * Get the ratings for this month.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}

	/**
	 * Get the item name.
	 *
	 * @return ItemName
	 */
	public function getItemName() : ItemName
	{
		return $this->itemName;
	}

	/**
	 * Get the item description.
	 *
	 * @return ItemDescription
	 */
	public function getItemDescription() : ItemDescription
	{
		return $this->itemDescription;
	}

	/**
	 * Get the item usage datas.
	 *
	 * @return ItemUsageData[]
	 */
	public function getItemUsageDatas() : array
	{
		return $this->itemUsageDatas;
	}
}
