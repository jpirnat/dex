<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Models;

use Jp\Dex\Domain\Forms\FormId;

interface ModelRepositoryInterface
{
	/**
	 * Get a model by its form, shininess, direction, gender, and attacking
	 * index.
	 *
	 * @param FormId $formId
	 * @param bool $isShiny
	 * @param bool $isBack
	 * @param bool $isFemale
	 * @param int $attackingIndex
	 *
	 * @throws ModelNotFoundException if no model exists with this form,
	 *     shininess, direction, gender, and attacking index.
	 *
	 * @return Model
	 */
	public function getByFormAndShinyAndBackAndFemaleAndAttackingIndex(
		FormId $formId,
		bool $isShiny,
		bool $isBack,
		bool $isFemale,
		int $attackingIndex
	) : Model;
}
