<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface;
use PDO;

class DatabaseMonthQueries implements MonthQueriesInterface
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
	 * Does usage data exist for this month?
	 *
	 * @param DateTime $month
	 *
	 * @return bool
	 */
	public function doesMonthDataExist(DateTime $month) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `usage`
			WHERE `month` = :month
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Does usage data exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function doesMonthFormatDataExist(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `usage`
			WHERE `month` = :month
				AND `format_id` = :format_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}
}
