<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\EggGroups\EggGroupName;
use Jp\Dex\Domain\EggGroups\EggGroupNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

class DatabaseEggGroupNameRepository implements EggGroupNameRepositoryInterface
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
	 * Get an egg group name by language and egg group.
	 *
	 * @param LanguageId $languageId
	 * @param EggGroupId $eggGroupId
	 *
	 * @return EggGroupName
	 */
	public function getByLanguageAndEggGroup(
		LanguageId $languageId,
		EggGroupId $eggGroupId
	) : EggGroupName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `egg_group_names`
			WHERE `language_id` = :language_id
				AND `egg_group_id` = :egg_group_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':egg_group_id', $eggGroupId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return new EggGroupName($languageId, $eggGroupId, '');
		}

		$eggGroupName = new EggGroupName(
			$languageId,
			$eggGroupId,
			$result['name']
		);

		return $eggGroupName;
	}
}
