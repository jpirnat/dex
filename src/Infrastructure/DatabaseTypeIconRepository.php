<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\TypeIcons\TypeIcon;
use Jp\Dex\Domain\TypeIcons\TypeIconNotFoundException;
use Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
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
	 * Get a type icon by its language and type.
	 *
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeIconNotFoundException if no type icon exists with this
	 *     language and type.
	 *
	 * @return TypeIcon
	 */
	public function getByLanguageAndType(LanguageId $languageId, TypeId $typeId) : TypeIcon
	{
		$stmt = $this->db->prepare(
			'SELECT
				`icon`
			FROM `type_icons`
			WHERE `language_id` = :language_id
				AND `type_id` = :type_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeIconNotFoundException(
				'No type icon exists with language id ' . $languageId->value()
				. ' and type id ' . $typeId->value() . '.'
			);
		}

		$typeIcon = new TypeIcon(
			$languageId,
			$typeId,
			$result['icon']
		);

		return $typeIcon;
	}

	/**
	 * Get type icons by their language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return TypeIcon[] Indexed by type id.
	 */
	public function getByLanguage(LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`type_id`,
				`icon`
			FROM `type_icons`
			WHERE `language_id` = :language_id'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeIcon = new TypeIcon(
				$languageId,
				new TypeId($result['type_id']),
				$result['icon']
			);

			$typeIcons[$result['type_id']] = $typeIcon;
		}

		return $typeIcons;
	}
}
