<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureName;
use Jp\Dex\Domain\Natures\NatureNameNotFoundException;
use Jp\Dex\Domain\Natures\NatureNameRepositoryInterface;
use PDO;

final class DatabaseNatureNameRepository implements NatureNameRepositoryInterface
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
	 * @throws NatureNameNotFoundException if no nature name exists for this
	 *     language and nature.
	 *
	 * @return NatureName
	 */
	public function getByLanguageAndNature(
		LanguageId $languageId,
		NatureId $natureId
	) : NatureName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`,
				`description`
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
			throw new NatureNameNotFoundException(
				'No nature name exists with language id '
				. $languageId->value() . ' and nature id '
				. $natureId->value()
			);
		}

		$natureName = new NatureName(
			$languageId,
			$natureId,
			$result['name'],
			$result['description']
		);

		return $natureName;
	}

	/**
	 * Get nature names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return NatureName[] Indexed by nature id.
	 */
	public function getByLanguage(LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`nature_id`,
				`name`,
				`description`
			FROM `nature_names`
			WHERE `language_id` = :language_id'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$natureNames = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$natureName = new NatureName(
				$languageId,
				new NatureId($result['nature_id']),
				$result['name'],
				$result['description']
			);

			$natureNames[$result['nature_id']] = $natureName;
		}

		return $natureNames;
	}
}
