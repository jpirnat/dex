<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use PDO;

final readonly class DatabaseNatureRepository implements NatureRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a nature by its identifier.
	 *
	 * @throws NatureNotFoundException if no nature exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Nature
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`increased_stat_id`,
				`decreased_stat_id`,
				`toxel_evo_id`,
				`vc_exp_remainder`
			FROM `natures`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new NatureNotFoundException(
				"No nature exists with identifier $identifier."
			);
		}

		return new Nature(
			new NatureId($result['id']),
			$identifier,
			$result['increased_stat_id'] !== null
				? new StatId($result['increased_stat_id'])
				: null,
			$result['decreased_stat_id'] !== null
				? new StatId($result['decreased_stat_id'])
				: null,
			new FormId($result['toxel_evo_id']),
			$result['vc_exp_remainder'],
		);
	}
}
