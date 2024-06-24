<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveName;
use Jp\Dex\Domain\Moves\MoveNameNotFoundException;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use PDO;

final readonly class DatabaseMoveNameRepository implements MoveNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a move name by language and move.
	 *
	 * @throws MoveNameNotFoundException if no move name exists for this
	 *     language and move.
	 */
	public function getByLanguageAndMove(
		LanguageId $languageId,
		MoveId $moveId,
	) : MoveName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `move_names`
			WHERE `language_id` = :language_id
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MoveNameNotFoundException(
				'No move name exists with language id '
				. $languageId->value() . ' and move id '
				. $moveId->value() . '.'
			);
		}

		$moveName = new MoveName(
			$languageId,
			$moveId,
			$result['name'],
		);

		return $moveName;
	}
}
