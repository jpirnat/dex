<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\Generation;

interface FormIconRepositoryInterface
{
	/**
	 * Get a form icon by its generation, form, gender, and direction.
	 *
	 * @param Generation $generation
	 * @param FormId $formId
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     generation, form, gender, and direction.
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
	 * Get form icons by their generation, gender, and direction. Indexed by
	 * form id.
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
