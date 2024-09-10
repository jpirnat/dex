<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Abilities\AbilityNameNotFoundException;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

final readonly class DatabaseAbilityNameRepository implements AbilityNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an ability name by language and ability.
	 *
	 * @throws AbilityNameNotFoundException if no ability name exists for this
	 *     language and ability.
	 */
	public function getByLanguageAndAbility(
		LanguageId $languageId,
		AbilityId $abilityId,
	) : AbilityName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `ability_names`
			WHERE `language_id` = :language_id
				AND `ability_id` = :ability_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new AbilityNameNotFoundException(
				'No ability name exists with language id '
				. $languageId->value() . ' and ability id '
				. $abilityId->value() . '.'
			);
		}

		return new AbilityName(
			$languageId,
			$abilityId,
			$result['name'],
		);
	}
}
