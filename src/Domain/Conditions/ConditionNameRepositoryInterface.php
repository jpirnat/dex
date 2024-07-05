<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Conditions;

use Jp\Dex\Domain\Languages\LanguageId;

interface ConditionNameRepositoryInterface
{
	/**
	 * Get a condition name by language and condition.
	 *
	 * @throws ConditionNameNotFoundException if no condition name exists for
	 *     this language and condition.
	 */
	public function getByLanguageAndCondition(
		LanguageId $languageId,
		ConditionId $conditionId,
	) : ConditionName;
}
