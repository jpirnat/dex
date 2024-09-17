<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Versions\VersionGroupId;

interface StatRepositoryInterface
{
	/**
	 * Get the stats in this version group.
	 *
	 * @return Stat[] Indexed by id. Ordered by sort value.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array;
}
