<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

interface AbilityRepositoryInterface
{
	/**
	 * Get an ability by its id.
	 *
	 * @throws AbilityNotFoundException if no ability exists with this id.
	 */
	public function getById(AbilityId $abilityId) : Ability;

	/**
	 * Get an ability by its identifier.
	 *
	 * @throws AbilityNotFoundException if no ability exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Ability;
}
