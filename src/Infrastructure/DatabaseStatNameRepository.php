<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatName;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use PDO;

final class DatabaseStatNameRepository implements StatNameRepositoryInterface
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
	 * Get stat names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return StatName[] Indexed by stat id.
	 */
	public function getByLanguage(LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`stat_id`,
				`name`,
				`abbreviation`
			FROM `stat_names`
			WHERE `language_id` = :language_id'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$statNames = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$statName = new StatName(
				$languageId,
				new StatId($result['stat_id']),
				$result['name'],
				$result['abbreviation']
			);

			$statNames[$result['stat_id']] = $statName;
		}

		return $statNames;
	}
}
