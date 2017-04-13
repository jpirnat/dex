<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Exception;

interface AbilityRepositoryInterface
{
	/**
	 * Get an ability by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no ability exists with this identifier.
	 *
	 * @return Ability
	 */
	public function getByIdentifier(string $identifier) : Ability;
}
