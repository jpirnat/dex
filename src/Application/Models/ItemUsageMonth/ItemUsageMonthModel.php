<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\ItemUsageMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;

class ItemUsageMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	private $itemRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var UsageRatedRepositoryInterface $usageRatedRepository */
	private $usageRatedRepository;

	/** @var UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository */
	private $usageRatedPokemonItemRepository;

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

	/** @var string $itemIdentifier */
	private $itemIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var ItemName $itemName */
	private $itemName;

	/** @var ItemUsageData[] $itemUsageDatas */
	private $itemUsageDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param ItemRepositoryInterface $itemRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param UsageRatedRepositoryInterface $usageRatedRepository
	 * @param UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		ItemRepositoryInterface $itemRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		UsageRatedRepositoryInterface $usageRatedRepository,
		UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->itemRepository = $itemRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->usageRatedRepository = $usageRatedRepository;
		$this->usageRatedPokemonItemRepository = $usageRatedPokemonItemRepository;
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
	 * @param string $itemIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		string $formatIdentifier,
		int $rating,
		string $itemIdentifier,
		LanguageId $languageId
	) : void {
		$this->year = $year;
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->itemIdentifier = $itemIdentifier;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($year, $month);
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

		// Get the item.
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		// Get the item name.
		$this->itemName = $this->itemNameRepository->getByLanguageAndItem(
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
				$format->getGeneration(),
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
	 * Get the item name.
	 *
	 * @return ItemName
	 */
	public function getItemName() : ItemName
	{
		return $this->itemName;
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
