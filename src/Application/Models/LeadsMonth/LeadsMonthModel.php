<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\LeadsMonth;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;

class LeadsMonthModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var LeadsRepositoryInterface $leadsRepository */
	private $leadsRepository;

	/** @var LeadsPokemonRepositoryInterface $leadsPokemonRepository */
	private $leadsPokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository */
	private $usageRatedPokemonRepository;

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

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var LeadsData[] $leadsDatas */
	private $leadsDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param LeadsRepositoryInterface $leadsRepository
	 * @param LeadsPokemonRepositoryInterface $leadsPokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		LeadsRepositoryInterface $leadsRepository,
		LeadsPokemonRepositoryInterface $leadsPokemonRepository,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->leadsRepository = $leadsRepository;
		$this->leadsPokemonRepository = $leadsPokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param string $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the previous month and the next month.
		$this->dateModel->setData($month);
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Does leads data exist for the previous month?
		$this->prevMonthDataExists = $this->leadsRepository->has(
			$prevMonth,
			$format->getId()
		);

		// Does leads data exist for the next month?
		$this->nextMonthDataExists = $this->leadsRepository->has(
			$nextMonth,
			$format->getId()
		);

		// Get leads Pokémon records for this month.
		$leadsPokemons = $this->leadsPokemonRepository->getByMonthAndFormat(
			$thisMonth,
			$format->getId()
		);

		// Get leads Pokémon records for the previous month.
		$prevMonthLeads = $this->leadsPokemonRepository->getByMonthAndFormat(
			$prevMonth,
			$format->getId()
		);

		// Get leads rated Pokémon records for this month.
		$leadsRatedPokemons = $this->leadsRatedPokemonRepository->getByMonthAndFormatAndRating(
			$thisMonth,
			$format->getId(),
			$rating
		);

		// Get leads rated Pokémon records for the previous month.
		$prevMonthRateds = $this->leadsRatedPokemonRepository->getByMonthAndFormatAndRating(
			$prevMonth,
			$format->getId(),
			$rating
		);

		// Get usage rated Pokémon records for this month (to determine whether
		// the moveset link should be shown).
		$usageRatedPokemons = $this->usageRatedPokemonRepository->getByMonthAndFormatAndRating(
			$thisMonth,
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByGenerationAndFemaleAndRight(
			$format->getGeneration(),
			false,
			false
		);

		// Get each leads record's data.
		foreach ($leadsRatedPokemons as $leadsRatedPokemon) {
			$pokemonId = $leadsRatedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for this month.
			$leadsPokemon = $leadsPokemons[$pokemonId->value()];

			// Get this Pokémon's change in usage percent from the previous month.
			$prevMonthUsagePercent = 0;
			if (isset($prevMonthRateds[$pokemonId->value()])) {
				$prevMonthUsagePercent = $prevMonthRateds[$pokemonId->value()]->getUsagePercent();
			}
			$usageChange = $leadsRatedPokemon->getUsagePercent() - $prevMonthUsagePercent;

			// Get this Pokémon's change in raw percent from the previous month.
			$prevMonthRawPercent = 0;
			if (isset($prevMonthLeads[$pokemonId->value()])) {
				$prevMonthRawPercent = $prevMonthLeads[$pokemonId->value()]->getRawPercent();
			}
			$rawChange = $leadsPokemon->getRawPercent() - $prevMonthRawPercent;

			// Get this Pokémon's rated usage record for this month.
			$usageRatedPokemon = $usageRatedPokemons[$pokemonId->value()];

			$this->leadsDatas[] = new LeadsData(
				$leadsRatedPokemon->getRank(),
				$pokemonName->getName(),
				$usageRatedPokemon->getUsagePercent(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$leadsRatedPokemon->getUsagePercent(),
				$usageChange,
				$leadsPokemon->getRaw(),
				$leadsPokemon->getRawPercent(),
				$rawChange
			);
		}
	}

	/**
	 * Does leads data exist for the previous month?
	 *
	 * @return bool
	 */
	public function doesPrevMonthDataExist() : bool
	{
		return $this->prevMonthDataExists;
	}

	/**
	 * Does leads data exist for the next month?
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
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the leads datas.
	 *
	 * @return LeadsData[]
	 */
	public function getLeadsDatas() : array
	{
		return $this->leadsDatas;
	}
}
