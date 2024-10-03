<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

interface EggGroupRepositoryInterface
{
	/**
	 * Get an egg group by its identifier.
	 *
	 * @throws EggGroupNotFoundException if no egg group exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : EggGroup;
}
