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
use Jp\Dex\Domain\Versions\VersionGroupId;

final class PokemonModel
{
	private DexPokemon $pokemon;
	private Model $model;


	public function __construct(
		private DexPokemonRepositoryInterface $dexPokemonRepository,
		private ModelRepositoryInterface $modelRepository,
	) {}


	/**
	 * Set miscellaneous data about the Pokémon (name, types, base stats, etc).
	 */
	public function setData(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : void {
		$this->pokemon = $this->dexPokemonRepository->getById(
			$versionGroupId,
			$pokemonId,
			$languageId,
		);

		// Get the Pokémon's model.
		$this->model = $this->modelRepository->getByFormAndShinyAndBackAndFemaleAndAttackingIndex(
			new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
			false,
			false,
			false,
			0,
		);
	}

	/**
	 * Get the Pokémon.
	 */
	public function getPokemon() : DexPokemon
	{
		return $this->pokemon;
	}

	/**
	 * Get the model.
	 */
	public function getModel() : Model
	{
		return $this->model;
	}
}
