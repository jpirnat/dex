<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureStatModifierRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use PDO;

class DatabaseNatureStatModifierRepository implements NatureStatModifierRepositoryInterface
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
	 * Get nature stat modifiers by nature.
	 *
	 * @param NatureId $natureId
	 *
	 * @return StatValueContainer
	 */
	public function getByNature(NatureId $natureId) : StatValueContainer
	{
		$stmt = $this->db->prepare(
			'SELECT
				`stat_id`,
				`modifier`
			FROM `nature_stat_modifiers`
			WHERE `nature_id` = :nature_id'
		);
		$stmt->bindValue(':nature_id', $natureId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$statModifiers = new StatValueContainer();

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$statModifier = new StatValue(
				new StatId($result['stat_id']),
				(float) $result['modifier']
			);

			$statModifiers->add($statModifier);
		}

		return $statModifiers;
	}
}
