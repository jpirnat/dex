<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeName;
use Jp\Dex\Domain\Types\TypeNameNotFoundException;
use Jp\Dex\Domain\Types\TypeNameRepositoryInterface;
use PDO;

final readonly class DatabaseTypeNameRepository implements TypeNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a type name by language and type.
	 *
	 * @throws TypeNameNotFoundException if no type name exists for this
	 *     language and type.
	 */
	public function getByLanguageAndType(
		LanguageId $languageId,
		TypeId $typeId,
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

		return new TypeName(
			$languageId,
			$typeId,
			$result['name'],
		);
	}
}
