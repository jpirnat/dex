<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Application\Models\DexPokemonTypesModel;
use Jp\Dex\Application\Models\Structs\DexPokemonType;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Models\Model;
use Jp\Dex\Domain\Models\ModelRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;

class PokemonModel
{
	/** @var DexPokemonTypesModel $dexPokemonTypesModel */
	private $dexPokemonTypesModel;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var ModelRepositoryInterface $modelRepository */
	private $modelRepository;

	/** @var BaseStatRepositoryInterface $baseStatRepository */
	private $baseStatRepository;

	/** @var StatNameRepositoryInterface $statNameRepository */
	private $statNameRepository;


	/** @var PokemonName $pokemon */
	private $pokemonName;

	/** @var Model $model */
	private $model;

	/** @var DexPokemonType[] $types */
	private $types = [];

	/** @var StatData[] $statDatas */
	private $statDatas = [];

	/**
	 * Constructor.
	 *
	 * @param DexPokemonTypesModel $dexPokemonTypesModel
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param ModelRepositoryInterface $modelRepository
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 * @param StatNameRepositoryInterface $statNameRepository
	 */
	public function __construct(
		DexPokemonTypesModel $dexPokemonTypesModel,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		ModelRepositoryInterface $modelRepository,
		BaseStatRepositoryInterface $baseStatRepository,
		StatNameRepositoryInterface $statNameRepository
	) {
		$this->dexPokemonTypesModel = $dexPokemonTypesModel;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->modelRepository = $modelRepository;
		$this->baseStatRepository = $baseStatRepository;
		$this->statNameRepository = $statNameRepository;
	}

	/**
	 * Set miscellaneous data about the Pokémon (name, types, base stats, etc).
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		Generation $generation,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get the Pokémon's name.
		$this->pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);

		// Get the Pokémon's model.
		$this->model = $this->modelRepository->getByFormAndShinyAndBackAndFemaleAndAttackingIndex(
			new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
			false,
			false,
			false,
			0
		);

		// Get the Pokémon's types.
		$this->types = $this->dexPokemonTypesModel->getDexPokemonTypes(
			$generation,
			$pokemonId,
			$languageId
		);

		// Get the Pokémon's base stats.
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$generation,
			$pokemonId
		);

		// Get the stat names.
		$statNames = $this->statNameRepository->getByLanguage($languageId);

		// Put the stat data together.
		$statIds = [
			new StatId(StatId::HP),
			new StatId(StatId::ATTACK),
			new StatId(StatId::DEFENSE),
			new StatId(StatId::SPECIAL_ATTACK),
			new StatId(StatId::SPECIAL_DEFENSE),
			new StatId(StatId::SPEED),
		];
		$this->statDatas = [];
		foreach ($statIds as $statId) {
			$this->statDatas[] = new StatData(
				$statNames[$statId->value()]->getName(),
				(int) $baseStats->get($statId)->getValue()
			);
		}
	}

	/**
	 * Get the Pokémon name.
	 *
	 * @return PokemonName
	 */
	public function getPokemonName() : PokemonName
	{
		return $this->pokemonName;
	}

	/**
	 * Get the model.
	 *
	 * @return Model
	 */
	public function getModel() : Model
	{
		return $this->model;
	}

	/**
	 * Get the types.
	 *
	 * @return DexPokemonType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * Get the stat datas.
	 *
	 * @return StatData[]
	 */
	public function getStatDatas() : array
	{
		return $this->statDatas;
	}
}
