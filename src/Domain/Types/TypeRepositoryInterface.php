<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Exception;

interface TypeRepositoryInterface
{
	/**
	 * Get a type by its hidden power index.
	 *
	 * @param int $hiddenPowerIndex
	 *
	 * @throws Exception if no type exists with this hidden power index.
	 *
	 * @return Type
	 */
	public function getByHiddenPowerIndex(int $hiddenPowerIndex) : Type;
}
