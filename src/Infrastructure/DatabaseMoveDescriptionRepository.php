<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveDescription;
use Jp\Dex\Domain\Moves\MoveDescriptionNotFoundException;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseMoveDescriptionRepository implements MoveDescriptionRepositoryInterface
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
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 *
	 * @throws MoveDescriptionNotFoundException if no move description exists
	 *     for this generation, language, and move.
	 *
	 * @return MoveDescription
	 */
	public function getByGenerationAndLanguageAndMove(
		Generation $generation,
		LanguageId $languageId,
		MoveId $moveId
	) : MoveDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `move_descriptions`
			WHERE `generation` = :generation
				AND `language_id` = :language_id
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MoveDescriptionNotFoundException(
				'No move description exists with generation '
				. $generation->getValue() . ', language id '
				. $languageId->value() . ', and move id ' . $moveId->value()
				. '.'
			);
		}

		$moveDescription = new MoveDescription(
			$generation,
			$languageId,
			$moveId,
			$result['description']
		);

		return $moveDescription;
	}
}
