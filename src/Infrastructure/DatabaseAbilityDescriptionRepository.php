<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionNotFoundException;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseAbilityDescriptionRepository implements AbilityDescriptionRepositoryInterface
{
	/** @var PDO $db */
	private $db;

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
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 *
	 * @throws AbilityDescriptionNotFoundException if no ability description
	 *     exists for this generation, language, and ability.
	 *
	 * @return AbilityDescription
	 */
	public function getByGenerationAndLanguageAndAbility(
		Generation $generation,
		LanguageId $languageId,
		AbilityId $abilityId
	) : AbilityDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `ability_descriptions`
			WHERE `generation` = :generation
				AND `language_id` = :language_id
				AND `ability_id` = :ability_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new AbilityDescriptionNotFoundException(
				'No ability description exists with generation '
				. $generation->getValue() . ', language id '
				. $languageId->value() . ', and ability id '
				. $abilityId->value() . '.'
			);
		}

		$abilityDescription = new AbilityDescription(
			$generation,
			$languageId,
			$abilityId,
			$result['description']
		);

		return $abilityDescription;
	}

	/**
	 * Get ability descriptions by generation and language.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 *
	 * @return AbilityDescription[] Indexed by ability id.
	 */
	public function getByGenerationAndLanguage(
		Generation $generation,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`ability_id`,
				`description`
			FROM `ability_descriptions`
			WHERE `generation` = :generation
				AND `language_id` = :language_id'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilityDescriptions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$abilityDescription = new AbilityDescription(
				$generation,
				$languageId,
				new AbilityId($result['ability_id']),
				$result['description']
			);

			$abilityDescriptions[$result['ability_id']] = $abilityDescription;
		}

		return $abilityDescriptions;
	}
}
