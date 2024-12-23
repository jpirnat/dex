<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class FormIcon
{
	public function __construct(
		private(set) VersionGroupId $versionGroupId,
		private(set) FormId $formId,
		private(set) bool $isFemale,
		private(set) bool $isRight,
		private(set) bool $isShiny,
		private(set) string $image,
	) {}
}
