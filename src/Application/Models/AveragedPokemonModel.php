<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Application\Models\StatsAveragedPokemon\AbilityModel;
use Jp\Dex\Application\Models\StatsAveragedPokemon\ItemModel;
use Jp\Dex\Application\Models\StatsAveragedPokemon\MoveModel;
use Jp\Dex\Application\Models\StatsPokemon\PokemonModel;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class AveragedPokemonModel
{
	private string $start;
	private string $end;
	private Format $format;
	private int $rating;
	private Pokemon $pokemon;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private VersionGroup $versionGroup;
	private Generation $generation;

	private array $abilities = [];
	private array $items = [];
	private array $moves = [];


	public function __construct(
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly VersionGroupRepositoryInterface $versionGroupRepository,
		private readonly GenerationRepositoryInterface $generationRepository,
		private readonly PokemonModel $pokemonModel,
		private readonly AbilityModel $abilityModel,
		private readonly ItemModel $itemModel,
		private readonly MoveModel $moveModel,
	) {}


	/**
	 * Set individual Pokémon usage data averaged over multiple months.
	 */
	public function setData(
		string $start,
		string $end,
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier,
		LanguageId $languageId,
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
			$languageId,
		);

		// Get the Pokémon.
		$this->pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Get the ratings for these months.
		$this->ratings = $this->ratingQueries->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId(),
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

		// Get ability data.
		$this->abilities = $this->abilityModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);

		// Get item data.
		$this->items = $this->itemModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);

		// Get move data.
		$this->moves = $this->moveModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);
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
	 * Get the ratings for these months.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
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

	public function getAbilities() : array
	{
		return $this->abilities;
	}

	public function getItems() : array
	{
		return $this->items;
	}

	public function getMoves() : array
	{
		return $this->moves;
	}
}
