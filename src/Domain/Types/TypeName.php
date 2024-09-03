<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class TypeName
{
	public function __construct(
		private LanguageId $languageId,
		private TypeId $typeId,
		private string $name,
	) {}

	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}

	public function getName() : string
	{
		return $this->name;
	}
}
