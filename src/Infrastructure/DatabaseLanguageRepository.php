<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\Language;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageNotFoundException;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use PDO;

final readonly class DatabaseLanguageRepository implements LanguageRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a language by its id.
	 *
	 * @throws LanguageNotFoundException if no language exists with this id.
	 */
	public function getById(LanguageId $languageId) : Language
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`locale`,
				`date_format`
			FROM `languages`
			WHERE `id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new LanguageNotFoundException(
				'No language exists with id ' . $languageId->value() . '.'
			);
		}

		return new Language(
			$languageId,
			$result['identifier'],
			$result['locale'],
			$result['date_format'],
		);
	}
}
