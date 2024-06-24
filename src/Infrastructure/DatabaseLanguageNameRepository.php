<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageName;
use Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface;
use PDO;

final readonly class DatabaseLanguageNameRepository implements LanguageNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get language names in their own languages.
	 *
	 * @return LanguageName[] Indexed by language id.
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
				$result['name'],
			);

			$languageNames[$result['named_language_id']] = $languageName;
		}

		return $languageNames;
	}
}
