<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface DexMoveRepositoryInterface
{
	/**
	 * Get a dex move by its id.
	 * This method is used to get data for the dex move page.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : DexMove;

	/**
	 * Get all dex moves in this version group.
	 * This method is used to get data for the dex moves page.
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get all dex moves with this move flag.
	 * This method is used to get data for the dex move flag page.
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function getByVgAndFlag(
		VersionGroupId $versionGroupId,
		MoveFlagId $flagId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get all dex moves learned by this Pokémon.
	 * This method is used to get data for the dex Pokémon page.
	 *
	 * @return DexMove[] Indexed by move id.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get all dex moves of this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function getByType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get dex moves for this version group's TMs.
	 *
	 * @return DexMove[] Indexed by move id.
	 */
	public function getTmsByVg(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;
}
