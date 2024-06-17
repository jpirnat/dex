<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseAbilityDescriptionRepository implements AbilityDescriptionRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an ability description by version group, language, and ability.
	 */
	public function getByAbility(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		AbilityId $abilityId,
	) : AbilityDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `ability_descriptions`
			WHERE `version_group_id` = :version_group_id
				AND `language_id` = :language_id
				AND `ability_id` = :ability_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return new AbilityDescription($versionGroupId, $languageId, $abilityId, '');
		}

		$abilityDescription = new AbilityDescription(
			$versionGroupId,
			$languageId,
			$abilityId,
			$result['description'],
		);

		return $abilityDescription;
	}
}
