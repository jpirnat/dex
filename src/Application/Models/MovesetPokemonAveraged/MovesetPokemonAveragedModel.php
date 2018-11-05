<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonAveraged;

use DateTime;
use Jp\Dex\Application\Models\MovesetPokemonMonth\PokemonModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;

class MovesetPokemonAveragedModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

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

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;


	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonModel $pokemonModel
	 * @param AbilityModel $abilityModel
	 * @param ItemModel $itemModel
	 * @param MoveModel $moveModel
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonModel $pokemonModel,
		AbilityModel $abilityModel,
		ItemModel $itemModel,
		MoveModel $moveModel
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
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
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->languageId = $languageId;

		// Get the start month and end month.
		$start = new DateTime("$start-01");
		$end = new DateTime("$end-01");

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get the Pokémon.
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Get Pokémon data.
		$this->pokemonModel->setData(
			$format->getGenerationId(),
			$pokemon->getId(),
			$languageId
		);

		// Get ability data.
		$this->abilityModel->setData(
			$start,
			$end,
			$format->getId(),
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get item data.
		$this->itemModel->setData(
			$start,
			$end,
			$format->getId(),
			$rating,
			$pokemon->getId(),
			$languageId
		);

		// Get move data.
		$this->moveModel->setData(
			$start,
			$end,
			$format->getId(),
			$rating,
			$pokemon->getId(),
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
	 * Get the Pokémon identifier.
	 *
	 * @return string
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
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
	 * Get the Pokémon model.
	 *
	 * @return PokemonModel
	 */
	public function getPokemonModel() : PokemonModel
	{
		return $this->pokemonModel;
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
