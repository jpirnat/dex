<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Languages\LanguageId;

interface VersionNameRepositoryInterface
{
	/**
	 * Get a version name by language and version.
	 *
	 * @throws VersionNameNotFoundException if no version name exists for this
	 *     language and version.
	 */
	public function getByLanguageAndVersion(
		LanguageId $languageId,
		VersionId $versionId,
	) : VersionName;
}
