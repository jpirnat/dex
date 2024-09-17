<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

interface NatureRepositoryInterface
{
	/**
	 * Get a nature by its identifier.
	 *
	 * @throws NatureNotFoundException if no nature exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Nature;
}
