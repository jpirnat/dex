<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface FormIconRepositoryInterface
{
	/**
	 * Get a form icon by its version group, form, gender, and direction.
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     version group, form, gender, and direction.
	 */
	public function getByVgAndFormAndFemaleAndRight(
		VersionGroupId $versionGroupId,
		FormId $formId,
		bool $isFemale,
		bool $isRight,
	) : FormIcon;

	/**
	 * Get form icons by their version group, gender, and direction.
	 *
	 * @return FormIcon[] Indexed by form id.
	 */
	public function getByVgAndFemaleAndRight(
		VersionGroupId $versionGroupId,
		bool $isFemale,
		bool $isRight,
	) : array;
}
