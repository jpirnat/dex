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
	private PDO $db;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

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

	/**
	 * Get ability descriptions by generation and language.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return AbilityDescription[] Indexed by ability id.
	 */
	public function getByGenerationAndLanguage(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`ability_id`,
				`description`
			FROM `ability_descriptions`
			WHERE `generation_id` = :generation_id
				AND `language_id` = :language_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilityDescriptions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$abilityDescription = new AbilityDescription(
				$generationId,
				$languageId,
				new AbilityId($result['ability_id']),
				$result['description']
			);

			$abilityDescriptions[$result['ability_id']] = $abilityDescription;
		}

		return $abilityDescriptions;
	}
}
