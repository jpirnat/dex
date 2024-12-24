<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Stat;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseStatRepository implements StatRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the stats in this version group.
	 *
	 * @return Stat[] Indexed by id. Ordered by sort value.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`
			FROM `stats`
			INNER JOIN `vg_stats` AS `vs`
				ON `id` = `vs`.`stat_id`
			WHERE `id` IN (
				SELECT
					`stat_id`
				FROM `vg_stats`
				WHERE `version_group_id` = :version_group_id
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->execute();

		$stats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$stat = new Stat(
				new StatId($result['id']),
				$result['identifier'],
			);

			$stats[$result['id']] = $stat;
		}

		return $stats;
	}
}
