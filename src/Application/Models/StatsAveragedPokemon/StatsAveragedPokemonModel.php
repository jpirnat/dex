<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

use DateTime;
use Jp\Dex\Application\Models\StatsPokemon\PokemonModel;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;

final class StatsAveragedPokemonModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var GenerationRepositoryInterface $generationRepository */
	private $generationRepository;


	/** @var PokemonModel $pokemonModel */
	private $pokemonModel;

	/** @var AbilityModel $abilityModel */
	private $abilityModel;

	/** @var ItemModel $itemModel */
	private $itemModel;

	/** @var MoveModel $moveModel */
	private $moveModel;


	/** @var string $start */
	private $start;

	/** @var string $end */
	private $end;

	/** @var Format $format */
	private $format;

	/** @var int $rating */
	private $rating;

	/** @var Pokemon $pokemon */
	private $pokemon;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var Generation $generation */
	private $generation;


	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param RatingQueriesInterface $ratingQueries
	 * @param GenerationRepositoryInterface $generationRepository
	 * @param PokemonModel $pokemonModel
	 * @param AbilityModel $abilityModel
	 * @param ItemModel $itemModel
	 * @param MoveModel $moveModel
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		RatingQueriesInterface $ratingQueries,
		GenerationRepositoryInterface $generationRepository,
		PokemonModel $pokemonModel,
		AbilityModel $abilityModel,
		ItemModel $itemModel,
		MoveModel $moveModel
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->ratingQueries = $ratingQueries;
		$this->generationRepository = $generationRepository;
		$this->pokemonModel = $pokemonModel;
		$this->abilityModel = $abilityModel;
		$this->itemModel = $itemModel;
		$this->moveModel = $moveModel;
	}

	/**
	 * Get moveset data averaged over multiple months.
	 *
	 * @param string $start
	 * @param string $end
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $pokemonIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $start,
		string $end,
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier,
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

		// Get the Pokémon.
		$this->pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Get the ratings for these months.
		$this->ratings = $this->ratingQueries->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId()
		);

		// Get Pokémon data.
		$this->pokemonModel->setData(
			$this->format->getGenerationId(),
			$this->pokemon->getId(),
			$languageId
		);

		// Get the format's generation.
		$this->generation = $this->generationRepository->getById(
			$this->format->getGenerationId()
		);

		// Get ability data.
		$this->abilityModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);

		// Get item data.
		$this->itemModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);

		// Get move data.
		$this->moveModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId
		);
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
	 * Get the Pokémon.
	 *
	 * @return Pokemon
	 */
	public function getPokemon() : Pokemon
	{
		return $this->pokemon;
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
	 * Get the Pokémon model.
	 *
	 * @return PokemonModel
	 */
	public function getPokemonModel() : PokemonModel
	{
		return $this->pokemonModel;
	}

	/**
	 * Get the generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the ability datas.
	 *
	 * @return AbilityData[]
	 */
	public function getAbilityDatas() : array
	{
		return $this->abilityModel->getAbilityDatas();
	}

	/**
	 * Get the the item datas.
	 *
	 * @return ItemData[]
	 */
	public function getItemDatas() : array
	{
		return $this->itemModel->getItemDatas();
	}

	/**
	 * Get the move datas.
	 *
	 * @return MoveData[]
	 */
	public function getMoveDatas() : array
	{
		return $this->moveModel->getMoveDatas();
	}
}
