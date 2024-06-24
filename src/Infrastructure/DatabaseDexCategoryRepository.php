<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Categories\DexCategoryRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

final readonly class DatabaseDexCategoryRepository implements DexCategoryRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get dex categories by their language.
	 *
	 * @return DexCategory[] Indexed by id.
	 */
	public function getByLanguage(LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`c`.`id`,
				`c`.`icon`,
				`n`.`name`
			FROM `categories` AS `c`
			INNER JOIN `category_names` AS `n`
				ON `c`.`id` = `n`.`category_id`
			WHERE `n`.`language_id` = :language_id'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexCategories = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexCategory = new DexCategory(
				$result['icon'],
				$result['name'],
			);

			$dexCategories[$result['id']] = $dexCategory;
		}

		return $dexCategories;
	}
}
