<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedLeads;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;

final class StatsAveragedLeadsModel
{
	private string $start;
	private string $end;
	private Format $format;
	private int $rating;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	/** @var LeadsData[] $leadsDatas */
	private array $leadsDatas = [];


	public function __construct(
		private FormatRepositoryInterface $formatRepository,
		private RatingQueriesInterface $ratingQueries,
		private LeadsAveragedPokemonRepositoryInterface $leadsAveragedPokemonRepository,
		private LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository,
		private MonthsCounter $monthsCounter,
		private PokemonRepositoryInterface $pokemonRepository,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private FormIconRepositoryInterface $formIconRepository,
	) {}


	/**
	 * Get leads data averaged over multiple months.
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

		// Get leads Pokémon records for these months.
		$leadsAveragedPokemons = $this->leadsAveragedPokemonRepository->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId()
		);

		// Get leads rated Pokémon records for these months.
		$leadsRatedAveragedPokemons = $this->leadsRatedAveragedPokemonRepository->getByMonthsAndFormatAndRating(
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

		// Get each leads record's data.
		foreach ($leadsRatedAveragedPokemons as $leadsRatedAveragedPokemon) {
			$pokemonId = $leadsRatedAveragedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon's number of months of moveset data.
			$months = $monthCounts[$pokemonId->value()] ?? 0;

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for these months.
			$leadsAveragedPokemon = $leadsAveragedPokemons[$pokemonId->value()];

			$this->leadsDatas[] = new LeadsData(
				$leadsRatedAveragedPokemon->getRank(),
				$pokemonName->getName(),
				$months,
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$leadsRatedAveragedPokemon->getUsagePercent(),
				$leadsAveragedPokemon->getRaw(),
				$leadsAveragedPokemon->getRawPercent()
			);
		}
	}


	/**
	 * Get the start month.
	 */
	public function getStart() : string
	{
		return $this->start;
	}

	/**
	 * Get the end month.
	 */
	public function getEnd() : string
	{
		return $this->end;
	}

	/**
	 * Get the format.
	 */
	public function getFormat() : Format
	{
		return $this->format;
	}

	/**
	 * Get the rating.
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the language id.
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
	 * Get the leads datas.
	 *
	 * @return LeadsData[]
	 */
	public function getLeadsDatas() : array
	{
		return $this->leadsDatas;
	}
}
