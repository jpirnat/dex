<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeName;
use Jp\Dex\Domain\Types\TypeNameNotFoundException;
use Jp\Dex\Domain\Types\TypeNameRepositoryInterface;
use PDO;

final class DatabaseTypeNameRepository implements TypeNameRepositoryInterface
{
	private PDO $db;

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
	 * Get a type name by language and type.
	 *
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeNameNotFoundException if no type name exists for this
	 *     language and type.
	 *
	 * @return TypeName
	 */
	public function getByLanguageAndType(
		LanguageId $languageId,
		TypeId $typeId
	) : TypeName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `type_names`
			WHERE `language_id` = :language_id
				AND `type_id` = :type_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeNameNotFoundException(
				'No type name exists with language id '
				. $languageId->value() . ' and type id '
				. $typeId->value() . '.'
			);
		}

		$typeName = new TypeName(
			$languageId,
			$typeId,
			$result['name']
		);

		return $typeName;
	}
}
