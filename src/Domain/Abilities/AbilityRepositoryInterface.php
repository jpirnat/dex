<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Versions\GenerationId;

interface AbilityRepositoryInterface
{
	/**
	 * Get an ability by its id.
	 *
	 * @param AbilityId $abilityId
	 *
	 * @throws AbilityNotFoundException if no ability exists with this id.
	 *
	 * @return Ability
	 */
	public function getById(AbilityId $abilityId) : Ability;

	/**
	 * Get an ability by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws AbilityNotFoundException if no ability exists with this
	 *     identifier.
	 *
	 * @return Ability
	 */
	public function getByIdentifier(string $identifier) : Ability;

	/**
	 * Get abilities in this generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return Ability[] Indexed by id.
	 */
	public function getByGeneration(GenerationId $generationId) : array;
}
