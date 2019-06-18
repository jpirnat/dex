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
	 * Get the previous month with usage data for any format.
	 *
	 * @param DateTime $month
	 *
	 * @return DateTime|null
	 */
	public function getPrev(DateTime $month) : ?DateTime
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage`
			WHERE `month` < :month
			ORDER BY `month` DESC
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->execute();
		$prevMonth = $stmt->fetchColumn();

		if (!$prevMonth) {
			return null;
		}

		return new DateTime($prevMonth);
	}

	/**
	 * Get the next month with usage data for any format.
	 *
	 * @param DateTime $month
	 *
	 * @return DateTime|null
	 */
	public function getNext(DateTime $month) : ?DateTime
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage`
			WHERE `month` > :month
			ORDER BY `month`
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->execute();
		$nextMonth = $stmt->fetchColumn();

		if (!$nextMonth) {
			return null;
		}

		return new DateTime($nextMonth);
	}

	/**
	 * Get the previous month with usage data for this format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return DateTime|null
	 */
	public function getPrevByFormat(DateTime $month, FormatId $formatId) : ?DateTime
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage`
			WHERE `month` < :month
				AND `format_id` = :format_id
			ORDER BY `month` DESC
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$prevMonth = $stmt->fetchColumn();

		if (!$prevMonth) {
			return null;
		}

		return new DateTime($prevMonth);
	}

	/**
	 * Get the next month with usage data for this format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return DateTime|null
	 */
	public function getNextByFormat(DateTime $month, FormatId $formatId) : ?DateTime
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage`
			WHERE `month` > :month
				AND `format_id` = :format_id
			ORDER BY `month`
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$nextMonth = $stmt->fetchColumn();

		if (!$nextMonth) {
			return null;
		}

		return new DateTime($nextMonth);
	}
}
