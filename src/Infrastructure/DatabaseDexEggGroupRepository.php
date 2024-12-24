<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\EggGroups\DexEggGroupRepositoryInterface;
use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\EggGroups\EggGroupNotFoundException;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;
use PDOStatement;

final readonly class DatabaseDexEggGroupRepository implements DexEggGroupRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	private function getBaseQuery() : string
	{
		return
"SELECT
	`e`.`identifier`,
	`n`.`name`
FROM `egg_groups` AS `e`
INNER JOIN `egg_group_names` AS `n`
	ON `e`.`id` = `n`.`egg_group_id`
";
	}

	/**
	 * @return DexEggGroup[]
	 */
	private function executeAndFetch(PDOStatement $stmt) : array
	{
		$stmt->execute();

		$eggGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$eggGroups[] = $this->fromRecord($result);
		}

		return $eggGroups;
	}

	private function fromRecord(array $result) : DexEggGroup
	{
		return new DexEggGroup(
			$result['identifier'],
			$result['name'],
		);
	}

	/**
	 * Get a dex egg group by its id.
	 *
	 * @throws EggGroupNotFoundException if no egg group exists with this id.
	 */
	public function getById(
		EggGroupId $eggGroupId,
		LanguageId $languageId,
	) : DexEggGroup {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `e`.`id` = :egg_group_id
	AND `n`.`language_id` = :language_id
LIMIT 1"
		);
		$stmt->bindValue(':egg_group_id', $eggGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new EggGroupNotFoundException(
				"No dex egg group exists with egg group id $eggGroupId->value and language id $languageId->value."
			);
		}

		return $this->fromRecord($result);
	}

	/**
	 * Get all dex egg groups.
	 *
	 * @return DexEggGroup[] Ordered by name.
	 */
	public function getAll(LanguageId $languageId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `n`.`language_id` = :language_id
ORDER BY `n`.`name`"
		);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		return $this->executeAndFetch($stmt);
	}
}
