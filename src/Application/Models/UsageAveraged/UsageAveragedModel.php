<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\UsageAveraged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemonRepositoryInterface;

class UsageAveragedModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var UsageAveragedPokemonRepositoryInterface $usageAveragedPokemonRepository */
	private $usageAveragedPokemonRepository;

	/** @var UsageRatedAveragedPokemonRepositoryInterface $usageRatedAveragedPokemonRepository */
	private $usageRatedAveragedPokemonRepository;

	/** @var LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository */
	private $leadsRatedAveragedPokemonRepository;

	/** @var MonthsCounter $monthsCounter */
	private $monthsCounter;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


	/** @var bool $leadsDataExists */
	private $leadsDataExists;

	/** @var string $start */
	private $start;

	/** @var string $end */
	private $end;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var UsageData[] $usageDatas */
	private $usageDatas = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param UsageAveragedPokemonRepositoryInterface $usageAveragedPokemonRepository
	 * @param UsageRatedAveragedPokemonRepositoryInterface $usageRatedAveragedPokemonRepository
	 * @param LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository
	 * @param MonthsCounter $monthsCounter
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		UsageAveragedPokemonRepositoryInterface $usageAveragedPokemonRepository,
		UsageRatedAveragedPokemonRepositoryInterface $usageRatedAveragedPokemonRepository,
		LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository,
		MonthsCounter $monthsCounter,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->usageAveragedPokemonRepository = $usageAveragedPokemonRepository;
		$this->usageRatedAveragedPokemonRepository = $usageRatedAveragedPokemonRepository;
		$this->leadsRatedAveragedPokemonRepository = $leadsRatedAveragedPokemonRepository;
		$this->monthsCounter = $monthsCounter;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get usage data averaged over multiple months.
	 *
	 * @param string $start
	 * @param string $end
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $start,
		string $end,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId
	) : void {
		$this->start = $start;
		$this->end = $end;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the start month and end month.
		$start = new DateTime("$start-01");
		$end = new DateTime("$end-01");

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Does leads rated data exist for this month?
		$this->leadsDataExists = $this->leadsRatedAveragedPokemonRepository->hasAny(
			$start,
			$end,
			$format->getId(),
			$rating
		);

		// Get usage Pokémon records for this month.
		$usageAveragedPokemons = $this->usageAveragedPokemonRepository->getByMonthsAndFormat(
			$start,
			$end,
			$format->getId()
		);

		// Get usage rated Pokémon records for this month.
		$usageRatedAveragedPokemons = $this->usageRatedAveragedPokemonRepository->getByMonthsAndFormatAndRating(
			$start,
			$end,
			$format->getId(),
			$rating
		);

		// Get each Pokémon's count of months with moveset data (to determine
		// whether the moveset link should be shown).
		$monthCounts = $this->monthsCounter->countMovesetMonthsAll(
			$start,
			$end,
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByGenerationAndFemaleAndRight(
			$format->getGenerationId(),
			false,
			false
		);

		// Get each usage record's data.
		foreach ($usageRatedAveragedPokemons as $usageRatedAveragedPokemon) {
			$pokemonId = $usageRatedAveragedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon's number of months of moveset data.
			$months = $monthCounts[$pokemonId->value()] ?? 0;

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for this month.
			$usageAveragedPokemon = $usageAveragedPokemons[$pokemonId->value()];

			$this->usageDatas[] = new UsageData(
				$usageRatedAveragedPokemon->getRank(),
				$pokemonName->getName(),
				$months,
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$usageRatedAveragedPokemon->getUsagePercent(),
				$usageAveragedPokemon->getRaw(),
				$usageAveragedPokemon->getRawPercent(),
				$usageAveragedPokemon->getReal(),
				$usageAveragedPokemon->getRealPercent()
			);
		}
	}

	/**
	 * Does leads rated data exist for these months?
	 *
	 * @return bool
	 */
	public function doesLeadsDataExist() : bool
	{
		return $this->leadsDataExists;
	}

	/**
	 * Get the start month.
	 *
	 * @return string
	 */
	public function getStart() : string
	{
		return $this->start;
	}

	/**
	 * Get the end month.
	 *
	 * @return string
	 */
	public function getEnd() : string
	{
		return $this->end;
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
	 * Get the usage datas.
	 *
	 * @return UsageData[]
	 */
	public function getUsageDatas() : array
	{
		return $this->usageDatas;
	}
}
