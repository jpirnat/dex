<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\Generation;

interface FormIconRepositoryInterface
{
	/**
	 * Get a form icon by its generation, form, whether it is female, and
	 * whether it is right.
	 *
	 * @param Generation $generation
	 * @param FormId $formId
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     generation, form, female-ness, and right-ness.
	 *
	 * @return FormIcon
	 */
	public function getByGenerationAndFormAndFemaleAndRight(
		Generation $generation,
		FormId $formId,
		bool $isFemale,
		bool $isRight
	) : FormIcon;

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
