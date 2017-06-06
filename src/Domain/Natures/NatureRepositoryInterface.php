<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

interface NatureRepositoryInterface
{
	/**
	 * Get a nature by its id.
	 *
	 * @param NatureId $natureId
	 *
	 * @throws NatureNotFoundException if no nature exists with this id.
	 *
	 * @return Nature
	 */
	public function getById(NatureId $natureId) : Nature;
}
