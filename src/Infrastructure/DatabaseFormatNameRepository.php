<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Formats\FormatNameNotFoundException;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

class DatabaseFormatNameRepository implements FormatNameRepositoryInterface
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
	 * Get a format name by language and format.
	 *
	 * @param LanguageId $languageId
	 * @param FormatId $formatId
	 *
	 * @throws FormatNameNotFoundException if no format name exists for this
	 *     language and format.
	 *
	 * @return FormatName
	 */
	public function getByLanguageAndFormat(
		LanguageId $languageId,
		FormatId $formatId
	) : FormatName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `format_names`
			WHERE `language_id` = :language_id
				AND `format_id` = :format_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormatNameNotFoundException(
				'No format name exists with language id '
				. $languageId->value() . ' and format id '
				. $formatId->value()
			);
		}

		$formatName = new FormatName(
			$languageId,
			$formatId,
			$result['name']
		);

		return $formatName;
	}
}
