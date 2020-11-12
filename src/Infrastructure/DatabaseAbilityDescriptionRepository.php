<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseAbilityDescriptionRepository implements AbilityDescriptionRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an ability description by generation, language, and ability.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 *
	 * @return AbilityDescription
	 */
	public function getByGenerationAndLanguageAndAbility(
		GenerationId $generationId,
		LanguageId $languageId,
		AbilityId $abilityId
	) : AbilityDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `ability_descriptions`
			WHERE `generation_id` = :generation_id
				AND `language_id` = :language_id
				AND `ability_id` = :ability_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return new AbilityDescription($generationId, $languageId, $abilityId, '');
		}

		$abilityDescription = new AbilityDescription(
			$generationId,
			$languageId,
			$abilityId,
			$result['description']
		);

		return $abilityDescription;
	}
}
