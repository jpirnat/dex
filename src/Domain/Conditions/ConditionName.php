<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Conditions;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class ConditionName
{
	public function __construct(
		private LanguageId $languageId,
		private ConditionId $conditionId,
		private string $name,
	) {}

	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	public function getConditionId() : ConditionId
	{
		return $this->conditionId;
	}

	public function getName() : string
	{
		return $this->name;
	}
}
