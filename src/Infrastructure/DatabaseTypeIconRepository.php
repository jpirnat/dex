<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\TypeIcons\TypeIcon;
use Jp\Dex\Domain\TypeIcons\TypeIconNotFoundException;
use Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseTypeIconRepository implements TypeIconRepositoryInterface
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
	 * Get a type icon by its generation, language, and type.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeIconNotFoundException if no type icon exists with this
	 *     generation, language, and type.
	 *
	 * @return TypeIcon
	 */
	public function getByGenerationAndLanguageAndType(
		Generation $generation,
		LanguageId $languageId,
		TypeId $typeId
	) : TypeIcon {
		$stmt = $this->db->prepare(
			'SELECT
				`image`
			FROM `type_icons`
			WHERE `generation` = :generation
				AND `language_id` = :language_id
				AND `type_id` = :type_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation', $generation->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeIconNotFoundException(
				'No type icon exists with generation ' . $generation->value()
				. ', language id ' . $languageId->value()
				. ', and type id ' . $typeId->value() . '.'
			);
		}

		$typeIcon = new TypeIcon(
			$generation,
			$languageId,
			$typeId,
			$result['image']
		);

		return $typeIcon;
	}

	/**
	 * Get type icons by their generation and language.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 *
	 * @return TypeIcon[] Indexed by type id.
	 */
	public function getByGenerationAndLanguage(
		Generation $generation,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`type_id`,
				`image`
			FROM `type_icons`
			WHERE `generation` = :generation
				AND `language_id` = :language_id'
		);
		$stmt->bindValue(':generation', $generation->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeIcon = new TypeIcon(
				$generation,
				$languageId,
				new TypeId($result['type_id']),
				$result['image']
			);

			$typeIcons[$result['type_id']] = $typeIcon;
		}

		return $typeIcons;
	}
}
