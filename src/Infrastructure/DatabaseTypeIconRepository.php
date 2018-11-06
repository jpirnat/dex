<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\TypeIcons\TypeIcon;
use Jp\Dex\Domain\TypeIcons\TypeIconNotFoundException;
use Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
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
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 *
	 * @throws TypeIconNotFoundException if no type icon exists with this
	 *     generation, language, and type.
	 *
	 * @return TypeIcon
	 */
	public function getByGenerationAndLanguageAndType(
		GenerationId $generationId,
		LanguageId $languageId,
		TypeId $typeId
	) : TypeIcon {
		// HACK: Type icons are only guaranteed to exist for every language in
		// the current generation.
		$generationId = new GenerationId(GenerationId::CURRENT);

		$stmt = $this->db->prepare(
			'SELECT
				`image`
			FROM `type_icons`
			WHERE `generation_id` = :generation_id
				AND `language_id` = :language_id
				AND `type_id` = :type_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeIconNotFoundException(
				'No type icon exists with generation id ' . $generationId->value()
				. ', language id ' . $languageId->value()
				. ', and type id ' . $typeId->value() . '.'
			);
		}

		$typeIcon = new TypeIcon(
			$generationId,
			$languageId,
			$typeId,
			$result['image']
		);

		return $typeIcon;
	}

	/**
	 * Get type icons by their generation and language.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return TypeIcon[] Indexed by type id.
	 */
	public function getByGenerationAndLanguage(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		// HACK: Type icons are only guaranteed to exist for every language in
		// the current generation.
		$generationId = new GenerationId(GenerationId::CURRENT);

		$stmt = $this->db->prepare(
			'SELECT
				`type_id`,
				`image`
			FROM `type_icons`
			WHERE `generation_id` = :generation_id
				AND `language_id` = :language_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeIcon = new TypeIcon(
				$generationId,
				$languageId,
				new TypeId($result['type_id']),
				$result['image']
			);

			$typeIcons[$result['type_id']] = $typeIcon;
		}

		return $typeIcons;
	}
}
