<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

interface DexMoveRepositoryInterface
{
	/**
	 * Get a dex move by its id.
	 * This method is used to get data for the dex move page.
	 */
	public function getById(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : DexMove;

	/**
	 * Get all dex moves in this generation.
	 * This method is used to get data for the dex moves page.
	 *
	 * @return DexMove[] Ordered by name.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex moves learned by this Pokémon.
	 * This method is used to get data for the dex Pokémon page.
	 *
	 * @return DexMove[]
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex moves of this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexMove[] Ordered by name.
	 */
	public function getByType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array;

}
