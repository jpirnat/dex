<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

final readonly class LanguageName
{
	public function __construct(
		private(set) LanguageId $inLanguageId,
		private(set) LanguageId $namedLanguageId,
		private(set) string $name,
	) {}
}
