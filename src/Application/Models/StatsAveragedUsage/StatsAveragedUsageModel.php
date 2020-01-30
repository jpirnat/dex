<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedUsage;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;

final class StatsAveragedUsageModel
{
	private FormatRepositoryInterface $formatRepository;
	private RatingQueriesInterface $ratingQueries;
	private UsageAveragedPokemonRepositoryInterface $usageAveragedPokemonRepository;
	private UsageRatedAveragedPokemonRepositoryInterface $usageRatedAveragedPokemonRepository;
	private LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository;
	private MonthsCounter $monthsCounter;
	private PokemonRepositoryInterface $pokemonRepository;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private FormIconRepositoryInterface $formIconRepository;


	private string $start;
	private string $end;
	private Format $format;
	private int $rating;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private bool $leadsDataExists;

	/** @var UsageData[] $usageDatas */
	private array $usageDatas = [];


	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param RatingQueriesInterface $ratingQueries
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
		RatingQueriesInterface $ratingQueries,
		UsageAveragedPokemonRepositoryInterface $usageAveragedPokemonRepository,
		UsageRatedAveragedPokemonRepositoryInterface $usageRatedAveragedPokemonRepository,
		LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository,
		MonthsCounter $monthsCounter,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->ratingQueries = $ratingQueries;
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
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the start month and end month.
		$start = new DateTime("$start-01");
		$end = new DateTime("$end-01");

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId
		);

		// Get the ratings for these months.
		$this->ratings = $this->ratingQueries->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId()
		);

		// Does leads rated data exist for these months?
		$this->leadsDataExists = $this->leadsRatedAveragedPokemonRepository->hasAny(
			$start,
			$end,
			$this->format->getId(),
			$rating
		);

		// Get usage Pokémon records for these months.
		$usageAveragedPokemons = $this->usageAveragedPokemonRepository->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId()
		);

		// Get usage rated Pokémon records for these months.
		$usageRatedAveragedPokemons = $this->usageRatedAveragedPokemonRepository->getByMonthsAndFormatAndRating(
			$start,
			$end,
			$this->format->getId(),
			$rating
		);

		// Get each Pokémon's count of months with moveset data (to determine
		// whether the moveset link should be shown).
		$monthCounts = $this->monthsCounter->countMovesetMonthsAll(
			$start,
			$end,
			$this->format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByGenerationAndFemaleAndRight(
			$this->format->getGenerationId(),
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

			// Get this Pokémon's non-rated usage record for these months.
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
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the ratings for these months.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
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
