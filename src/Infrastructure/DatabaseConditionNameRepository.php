<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Conditions\ConditionId;
use Jp\Dex\Domain\Conditions\ConditionName;
use Jp\Dex\Domain\Conditions\ConditionNameNotFoundException;
use Jp\Dex\Domain\Conditions\ConditionNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

final readonly class DatabaseConditionNameRepository implements ConditionNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a condition name by language and condition.
	 *
	 * @throws ConditionNameNotFoundException if no condition name exists for
	 *     this language and condition.
	 */
	public function getByLanguageAndCondition(
		LanguageId $languageId,
		ConditionId $conditionId,
	) : ConditionName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `condition_names`
			WHERE `language_id` = :language_id
				AND `condition_id` = :condition_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->bindValue(':condition_id', $conditionId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ConditionNameNotFoundException(
				"No condition name exists with language id $languageId->value and condition id $conditionId->value."
			);
		}

		return new ConditionName(
			$languageId,
			$conditionId,
			$result['name'],
		);
	}
}
