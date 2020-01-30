<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Models\Model;
use Jp\Dex\Domain\Models\ModelRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class PokemonModel
{
	private DexPokemonRepositoryInterface $dexPokemonRepository;
	private ModelRepositoryInterface $modelRepository;
	private StatNameRepositoryInterface $statNameRepository;


	private DexPokemon $pokemon;
	private Model $model;

	/** @var StatData[] $statDatas */
	private array $statDatas = [];


	/**
	 * Constructor.
	 *
	 * @param DexPokemonRepositoryInterface $dexPokemonRepository
	 * @param ModelRepositoryInterface $modelRepository
	 * @param StatNameRepositoryInterface $statNameRepository
	 */
	public function __construct(
		DexPokemonRepositoryInterface $dexPokemonRepository,
		ModelRepositoryInterface $modelRepository,
		StatNameRepositoryInterface $statNameRepository
	) {
		$this->dexPokemonRepository = $dexPokemonRepository;
		$this->modelRepository = $modelRepository;
		$this->statNameRepository = $statNameRepository;
	}

	/**
	 * Set miscellaneous data about the Pokémon (name, types, base stats, etc).
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		$this->pokemon = $this->dexPokemonRepository->getById(
			$generationId,
			$pokemonId,
			$languageId
		);

		// Get the Pokémon's model.
		$this->model = $this->modelRepository->getByFormAndShinyAndBackAndFemaleAndAttackingIndex(
			new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
			false,
			false,
			false,
			0
		);

		// Get the Pokémon's base stats.
		$statIds = StatId::getByGeneration($generationId);
		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$baseStats = $this->pokemon->getBaseStats();
		$normalizedStatNames = [];
		foreach ($statIds as $statId) {
			$normalizedStatNames[] = $statNames[$statId->value()]->getName();
		}
		foreach ($baseStats as $index => $baseStat) {
			$this->statDatas[] = new StatData(
				$normalizedStatNames[$index],
				(int) $baseStat
			);
		}
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return DexPokemon
	 */
	public function getPokemon() : DexPokemon
	{
		return $this->pokemon;
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
	 * Get the stat datas.
	 *
	 * @return StatData[]
	 */
	public function getStatDatas() : array
	{
		return $this->statDatas;
	}
}
