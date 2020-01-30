<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodName;
use Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface;
use PDO;

final class DatabaseMoveMethodNameRepository implements MoveMethodNameRepositoryInterface
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
	 * Get move method names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return MoveMethodName[] Indexed by move method id.
	 */
	public function getByLanguage(LanguageId $languageId) : array
	{
		// HACK: Move method names currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`move_method_id`,
				`name`,
				`description`
			FROM `move_method_names`
			WHERE `language_id` = :language_id'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$moveMethodNames = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$moveMethodName = new MoveMethodName(
				$languageId,
				new MoveMethodId($result['move_method_id']),
				$result['name'],
				$result['description']
			);

			$moveMethodNames[$result['move_method_id']] = $moveMethodName;
		}

		return $moveMethodNames;
	}
}
