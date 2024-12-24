<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class TypeName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) TypeId $typeId,
		private(set) string $name,
	) {}
}
