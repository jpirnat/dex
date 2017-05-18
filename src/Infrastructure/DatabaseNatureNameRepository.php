<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureName;
use Jp\Dex\Domain\Natures\NatureNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

class DatabaseNatureNameRepository implements NatureNameRepositoryInterface
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
	 * Get a nature name by language and nature.
	 *
	 * @param LanguageId $languageId
	 * @param NatureId $natureId
	 *
	 * @throws Exception if no name exists.
	 *
	 * @return NatureName
	 */
	public function getByLanguageAndNature(
		LanguageId $languageId,
		NatureId $natureId
	) : NatureName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `nature_names`
			WHERE `language_id` = :language_id
				AND `nature_id` = :nature_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':nature_id', $natureId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new Exception(
				'No nature name exists with language id '
				. $languageId->value() . ' and nature id '
				. $natureId->value()
			);
		}

		$natureName = new NatureName(
			$languageId,
			$natureId,
			$result['name']
		);

		return $natureName;
	}
}
