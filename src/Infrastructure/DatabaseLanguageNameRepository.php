<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageName;
use Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface;
use PDO;

final class DatabaseLanguageNameRepository implements LanguageNameRepositoryInterface
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
	 * Get language names in their own languages. Indexed by language id value.
	 *
	 * @return LanguageName[]
	 */
	public function getInOwnLanguages() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`in_language_id`,
				`named_language_id`,
				`name`
			FROM `language_names`
			WHERE `in_language_id` = `named_language_id`'
		);
		$stmt->execute();

		$languageNames = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$languageName = new LanguageName(
				new LanguageId($result['in_language_id']),
				new LanguageId($result['named_language_id']),
				$result['name']
			);

			$languageNames[$result['named_language_id']] = $languageName;
		}

		return $languageNames;
	}
}
