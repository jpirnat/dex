<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Versions\Generation;

interface FormIconRepositoryInterface
{
	/**
	 * Get form icons by their generation, whether they are female, and whether
	 * they are right. Indexed by form id.
	 *
	 * @param Generation $generation
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @return FormIcon[]
	 */
	public function getByGenerationAndFemaleAndRight(
		Generation $generation,
		bool $isFemale,
		bool $isRight
	) : array;
}
