<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

final readonly class Language
{
	public function __construct(
		private(set) LanguageId $id,
		private(set) string $identifier,
		private(set) string $locale,
		private(set) string $dateFormat,
	) {}
}
