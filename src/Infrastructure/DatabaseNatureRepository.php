<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use PDO;

class DatabaseNatureRepository implements NatureRepositoryInterface
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
	 * Get a nature by its id.
	 *
	 * @param NatureId $natureId
	 *
	 * @throws NatureNotFoundException if no nature exists with this id.
	 *
	 * @return Nature
	 */
	public function getById(NatureId $natureId) : Nature
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`increased_stat_id`,
				`decreased_stat_id`
			FROM `natures`
			WHERE `id` = :nature_id
			LIMIT 1'
		);
		$stmt->bindValue(':nature_id', $natureId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new NatureNotFoundException(
				'No nature exists with id ' . $$natureId->value()
			);
		}

		$statModifiers = new StatValueContainer();
		$statModifiers->add(new StatValue(new StatId(StatId::ATTACK), 1));
		$statModifiers->add(new StatValue(new StatId(StatId::DEFENSE), 1));
		$statModifiers->add(new StatValue(new StatId(StatId::SPECIAL_ATTACK), 1));
		$statModifiers->add(new StatValue(new StatId(StatId::SPECIAL_DEFENSE), 1));
		$statModifiers->add(new StatValue(new StatId(StatId::SPEED), 1));
		// And then overwrite with the real modifiers.
		if ($result['increased_stat_id'] !== null) {
			$statModifiers->add(new StatValue(new StatId($result['increased_stat_id']), 1.1));
		}
		if ($result['decreased_stat_id'] !== null) {
			$statModifiers->add(new StatValue(new StatId($result['decreased_stat_id']), 0.9));
		}

		$nature = new Nature(
			$natureId,
			$result['identifier'],
			$statModifiers
		);

		return $nature;
	}
}
