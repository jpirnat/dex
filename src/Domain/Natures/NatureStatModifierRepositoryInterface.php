<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Stats\StatValueContainer;

interface NatureStatModifierRepositoryInterface
{
	/**
	 * Get nature stat modifiers by nature.
	 *
	 * @param NatureId $natureId
	 *
	 * @return StatValueContainer
	 */
	public function getByNature(NatureId $natureId) : StatValueContainer;
}
