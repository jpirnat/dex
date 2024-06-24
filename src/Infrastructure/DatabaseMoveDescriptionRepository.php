<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveDescription;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseMoveDescriptionRepository implements MoveDescriptionRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a move description by version group, language, and move.
	 */
	public function getByMove(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		MoveId $moveId,
	) : MoveDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `move_descriptions`
			WHERE `version_group_id` = :version_group_id
				AND `language_id` = :language_id
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return new MoveDescription($versionGroupId, $languageId, $moveId, '');
		}

		$moveDescription = new MoveDescription(
			$versionGroupId,
			$languageId,
			$moveId,
			$result['description'],
		);

		return $moveDescription;
	}
}
