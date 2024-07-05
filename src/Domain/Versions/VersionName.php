<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class VersionName
{
	public function __construct(
		private LanguageId $languageId,
		private VersionId $versionId,
		private string $name,
	) {}

	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	public function getVersionId() : VersionId
	{
		return $this->versionId;
	}

	public function getName() : string
	{
		return $this->name;
	}
}
