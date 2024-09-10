<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Models\ModelNotFoundException;
use Jp\Dex\Domain\Models\ModelRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class PokemonModel
{
	private DexPokemon $pokemon;
	private string $image = '';
	private array $baseStats = [];


	public function __construct(
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly ModelRepositoryInterface $modelRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
	) {}


	/**
	 * Set miscellaneous data about the Pokémon (name, types, base stats, etc).
	 */
	public function setData(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : void {
		$this->image = '';
		$this->baseStats = [];

		$this->pokemon = $this->dexPokemonRepository->getById(
			$versionGroupId,
			$pokemonId,
			$languageId,
		);

		// Get the Pokémon's model.
		try {
			$model = $this->modelRepository->getByFormAndShinyAndBackAndFemaleAndAttackingIndex(
				new FormId($pokemonId->value()), // A Pokémon's default form has Pokémon id === form id.
				false,
				false,
				false,
				0,
			);
			$image = $model->getImage();
			$this->image = "models/$image";
		} catch (ModelNotFoundException) {
		}

		$this->baseStats = $this->dexStatRepository->getBaseStats(
			$versionGroupId,
			$pokemonId,
			$languageId,
		);
	}

	public function getPokemon() : DexPokemon
	{
		return $this->pokemon;
	}

	public function getImage() : string
	{
		return $this->image;
	}

	public function getBaseStats() : array
	{
		return $this->baseStats;
	}
}
