<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionId;
use Jp\Dex\Domain\Versions\VersionName;
use Jp\Dex\Domain\Versions\VersionNameNotFoundException;
use Jp\Dex\Domain\Versions\VersionNameRepositoryInterface;
use PDO;

final readonly class DatabaseVersionNameRepository implements VersionNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a version name by language and version.
	 *
	 * @throws VersionNameNotFoundException if no version name exists for this
	 *     language and version.
	 */
	public function getByLanguageAndVersion(
		LanguageId $languageId,
		VersionId $versionId,
	) : VersionName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `version_names`
			WHERE `language_id` = :language_id
				AND `version_id` = :version_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_id', $versionId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VersionNameNotFoundException(
				'No version name exists with language id '
				. $languageId->value() . ' and version id '
				. $versionId->value() . '.'
			);
		}

		return new VersionName(
			$languageId,
			$versionId,
			$result['name'],
		);
	}
}
