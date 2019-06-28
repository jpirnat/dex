<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNotFoundException;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseFormatRepository implements FormatRepositoryInterface
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
	 * Get a format by its id.
	 *
	 * @param FormatId $formatId
	 * @param LanguageId $languageId
	 *
	 * @throws FormatNotFoundException if no format exists with this id.
	 *
	 * @return Format
	 */
	public function getById(FormatId $formatId, LanguageId $languageId) : Format
	{
		// HACK: Format names currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`identifier`,
				`fn`.`name`,
				`f`.`generation_id`,
				`f`.`level`,
				`f`.`field_size`,
				`f`.`smogon_dex_identifier`
			FROM `formats` AS `f`
			INNER JOIN `format_names` AS `fn`
				ON `f`.`id` = `fn`.`format_id`
			WHERE `f`.`id` = :format_id
				AND `fn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_STR);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormatNotFoundException(
				'No format exists with id ' . $formatId->value()
			);
		}

		$format = new Format(
			$formatId,
			$result['identifier'],
			$result['name'],
			new GenerationId($result['generation_id']),
			$result['level'],
			$result['field_size'],
			$result['smogon_dex_identifier']
		);

		return $format;
	}

	/**
	 * Get a format by its identifier.
	 *
	 * @param string $identifier
	 * @param LanguageId $languageId
	 *
	 * @throws FormatNotFoundException if no format exists with this identifier.
	 *
	 * @return Format
	 */
	public function getByIdentifier(
		string $identifier,
		LanguageId $languageId
	) : Format {
		// HACK: Format names currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`id`,
				`fn`.`name`,
				`f`.`generation_id`,
				`f`.`level`,
				`f`.`field_size`,
				`f`.`smogon_dex_identifier`
			FROM `formats` AS `f`
			INNER JOIN `format_names` AS `fn`
				ON `f`.`id` = `fn`.`format_id`
			WHERE `f`.`identifier` = :identifier
				AND `fn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormatNotFoundException(
				'No format exists with identifier ' . $identifier
			);
		}

		$format = new Format(
			new FormatId($result['id']),
			$identifier,
			$result['name'],
			new GenerationId($result['generation_id']),
			$result['level'],
			$result['field_size'],
			$result['smogon_dex_identifier']
		);

		return $format;
	}
}
