<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;

interface DexTypeRepositoryInterface
{
	/**
	 * Get all dex types available in this generation.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array;
}
