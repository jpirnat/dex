<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\DexNatureRepositoryInterface;
use PDO;

final readonly class DatabaseDexNatureRepository implements DexNatureRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the dex natures by language.
	 */
	public function getByLanguage(LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`nn`.`name`,
				`sn1`.`name` AS `increasedStat`,
				`sn2`.`name` AS `decreasedStat`,
				`n`.`vc_exp_remainder` AS `vcExpRemainder`
			FROM `natures` AS `n`
			INNER JOIN `nature_names` AS `nn`
				ON `n`.`id` = `nn`.`nature_id`
			LEFT JOIN `stat_names` AS `sn1`
				ON `n`.`increased_stat_id` = `sn1`.`stat_id`
				AND `nn`.`language_id` = `sn1`.`language_id`
			LEFT JOIN `stat_names` AS `sn2`
				ON `n`.`decreased_stat_id` = `sn2`.`stat_id`
				AND `nn`.`language_id` = `sn2`.`language_id`
			WHERE `nn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
