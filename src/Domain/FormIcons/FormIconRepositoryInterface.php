<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\GenerationId;

interface FormIconRepositoryInterface
{
	/**
	 * Get a form icon by its generation, form, gender, and direction.
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     generation, form, gender, and direction.
	 */
	public function getByGenerationAndFormAndFemaleAndRight(
		GenerationId $generationId,
		FormId $formId,
		bool $isFemale,
		bool $isRight
	) : FormIcon;

	/**
	 * Get form icons by their generation, gender, and direction.
	 *
	 * @return FormIcon[] Indexed by form id.
	 */
	public function getByGenerationAndFemaleAndRight(
		GenerationId $generationId,
		bool $isFemale,
		bool $isRight
	) : array;
}
