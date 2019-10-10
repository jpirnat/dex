<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveDescription;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseMoveDescriptionRepository implements MoveDescriptionRepositoryInterface
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
	 * Get a move description by generation, language, and move.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 *
	 * @return MoveDescription
	 */
	public function getByGenerationAndLanguageAndMove(
		GenerationId $generationId,
		LanguageId $languageId,
		MoveId $moveId
	) : MoveDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `move_descriptions`
			WHERE `generation_id` = :generation_id
				AND `language_id` = :language_id
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return new MoveDescription($generationId, $languageId, $moveId, '');
		}

		$moveDescription = new MoveDescription(
			$generationId,
			$languageId,
			$moveId,
			$result['description']
		);

		return $moveDescription;
	}
}
