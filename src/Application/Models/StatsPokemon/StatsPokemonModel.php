<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use DateTime;
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
use Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface;
use Jp\Dex\Domain\Teammates\StatsPokemonTeammate;
use Jp\Dex\Domain\Teammates\StatsPokemonTeammateRepositoryInterface;
use Jp\Dex\Domain\Types\StatsPokemonTeraType;
use Jp\Dex\Domain\Types\StatsPokemonTeraTypeRepositoryInterface;
use Jp\Dex\Domain\Usage\StatsUsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class StatsPokemonModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private Pokemon $pokemon;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private ?array $prevRank = null;
	private ?array $thisRank = null;
	private ?array $nextRank = null;

	private ?MovesetPokemon $movesetPokemon;
	private ?MovesetRatedPokemon $movesetRatedPokemon;
	private VersionGroup $versionGroup;
	private Generation $generation;

	/** @var StatsPokemonAbility[] $abilities */
	private array $abilities = [];

	/** @var StatsPokemonItem[] $items */
	private array $items = [];

	/** @var StatsPokemonMove[] $moves */
	private array $moves = [];

	/** @var StatsPokemonTeraType[] $teraTypes */
	private array $teraTypes = [];

	/** @var StatsPokemonTeammate[] $teammates */
	private array $teammates = [];

	/** @var StatsPokemonCounter[] $counters */
	private array $counters = [];

	/** @var DateTime[] $months */
	private array $months = [];


	public function __construct(
		private readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly StatsUsagePokemonRepositoryInterface $statsUsagePokemonRepository,
		private readonly VersionGroupRepositoryInterface $versionGroupRepository,
		private readonly GenerationRepositoryInterface $generationRepository,
		private readonly MovesetPokemonRepositoryInterface $movesetPokemonRepository,
		private readonly MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		private readonly PokemonModel $pokemonModel,
		private readonly StatsPokemonAbilityRepositoryInterface $statsPokemonAbilityRepository,
		private readonly StatsPokemonItemRepositoryInterface $statsPokemonItemRepository,
		private readonly SpreadModel $spreadModel,
		private readonly StatsPokemonMoveRepositoryInterface $statsPokemonMoveRepository,
		private readonly StatsPokemonTeraTypeRepositoryInterface $statsPokemonTeraTypeRepository,
		private readonly StatsPokemonTeammateRepositoryInterface $statsPokemonTeammateRepository,
		private readonly StatsPokemonCounterRepositoryInterface $statsPokemonCounterRepository,
		private readonly UsageRatedQueriesInterface $usageRatedQueries,
	) {}


	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier,
		LanguageId $languageId,
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
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
			$this->format->getId(),
		);

		// Get the previous and next ranked Pokémon.
		$this->thisRank = $this->statsUsagePokemonRepository->getByPokemon(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getVersionGroupId(),
			$languageId,
		);
		$this->prevRank = $this->statsUsagePokemonRepository->getByRank(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->thisRank['rank'] - 1,
			$this->format->getVersionGroupId(),
			$languageId,
		);
		$this->nextRank = $this->statsUsagePokemonRepository->getByRank(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->thisRank['rank'] + 1,
			$this->format->getVersionGroupId(),
			$languageId,
		);


		// Get Pokémon data.
		$this->pokemonModel->setData(
			$this->format->getVersionGroupId(),
			$this->pokemon->getId(),
			$languageId,
		);

		// Get the format's version group and generation.
		$this->versionGroup = $this->versionGroupRepository->getById($this->format->getVersionGroupId());
		$this->generation = $this->generationRepository->getById($this->versionGroup->getGenerationId());

		// Get the moveset Pokémon record.
		$this->movesetPokemon = $this->movesetPokemonRepository->getByMonthAndFormatAndPokemon(
			$thisMonth,
			$this->format->getId(),
			$this->pokemon->getId(),
		);

		// Get moveset rated Pokémon record.
		$this->movesetRatedPokemon = $this->movesetRatedPokemonRepository->getByMonthAndFormatAndRatingAndPokemon(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
		);

		// Get ability data.
		$this->abilities = $this->statsPokemonAbilityRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);

		// Get item data.
		$this->items = $this->statsPokemonItemRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getVersionGroupId(),
			$languageId,
		);

		// Get spread data.
		$this->spreadModel->setData(
			$thisMonth,
			$this->format,
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);

		// Get move data.
		$this->moves = $this->statsPokemonMoveRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getVersionGroupId(),
			$languageId,
		);

		// Get Tera type data.
		if ($this->format->getVersionGroupId()->hasTeraTypes()) {
			$this->teraTypes = $this->statsPokemonTeraTypeRepository->getByMonth(
				$thisMonth,
				$prevMonth,
				$this->format->getId(),
				$rating,
				$this->pokemon->getId(),
				$languageId,
			);
		}

		// Get teammate data.
		$this->teammates = $this->statsPokemonTeammateRepository->getByMonth(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getVersionGroupId(),
			$languageId,
		);

		// Get counter data.
		$this->counters = $this->statsPokemonCounterRepository->getByMonth(
			$thisMonth,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$this->format->getVersionGroupId(),
			$languageId,
		);

		$this->months = $this->usageRatedQueries->getMonthsWithData(
			$this->format->getId(),
			$rating,
		);
	}


	/**
	 * Get the month.
	 */
	public function getMonth() : string
	{
		return $this->month;
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
	 * Get the Pokémon.
	 */
	public function getPokemon() : Pokemon
	{
		return $this->pokemon;
	}

	/**
	 * Get the language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the date model.
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
	 * Get the previous rank Pokémon.
	 */
	public function getPrevRank() : ?array
	{
		return $this->prevRank;
	}

	/**
	 * Get this Pokémon's usage rank.
	 */
	public function getThisRank() : ?array
	{
		return $this->thisRank;
	}

	/**
	 * Get the next rank Pokémon.
	 */
	public function getNextRank() : ?array
	{
		return $this->nextRank;
	}

	/**
	 * Get the Pokémon model.
	 */
	public function getPokemonModel() : PokemonModel
	{
		return $this->pokemonModel;
	}

	/**
	 * Get the version group.
	 */
	public function getVersionGroup() : VersionGroup
	{
		return $this->versionGroup;
	}

	/**
	 * Get the generation.
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the moveset Pokémon record.
	 */
	public function getMovesetPokemon() : ?MovesetPokemon
	{
		return $this->movesetPokemon;
	}

	/**
	 * Get the moveset rated Pokémon record.
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
	 * Get the spread model.
	 */
	public function getSpreadModel() : SpreadModel
	{
		return $this->spreadModel;
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
	 * Get the Tera types.
	 *
	 * @return StatsPokemonTeraType[]
	 */
	public function getTeraTypes() : array
	{
		return $this->teraTypes;
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

	/**
	 * Get the months.
	 *
	 * @return DateTime[]
	 */
	public function getMonths() : array
	{
		return $this->months;
	}
}
