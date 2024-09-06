<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatName;
use Jp\Dex\Domain\Stats\StatNameNotFoundException;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use PDO;

final readonly class DatabaseStatNameRepository implements StatNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a stat name by language and stat.
	 *
	 * @throws StatNameNotFoundException if no stat name exists with this
	 *     language and stat.
	 */
	public function getByLanguageAndStat(
		LanguageId $languageId,
		StatId $statId,
	) : StatName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`,
				`abbreviation`
			FROM `stat_names`
			WHERE `language_id` = :language_id
				AND `stat_id` = :stat_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':stat_id', $statId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new StatNameNotFoundException(
				'No stat name exists with language id ' . $languageId->value()
				. ' and stat id ' . $statId->value() . '.'
			);
		}

		return new StatName(
			$languageId,
			$statId,
			$result['name'],
			$result['abbreviation'],
		);
	}

	/**
	 * Get stat names by language.
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
				$result['abbreviation'],
			);

			$statNames[$result['stat_id']] = $statName;
		}

		return $statNames;
	}
}
