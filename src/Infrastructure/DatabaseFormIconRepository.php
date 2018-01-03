<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\FormIcons\FormIcon;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseFormIconRepository implements FormIconRepositoryInterface
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
	 * Get form icons by their generation, whether they are female, and whether
	 * they are right. Indexed by form id.
	 *
	 * @param Generation $generation
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @return FormIcon[]
	 */
	public function getByGenerationAndFemaleAndRight(
		Generation $generation,
		bool $isFemale,
		bool $isRight
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`form_id`,
				`image`
			FROM `form_icons`
			WHERE `generation` = :generation
				AND `is_female` = :is_female
				AND `is_right` = :is_right'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':is_female', false, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', false, PDO::PARAM_INT);
		$stmt->execute();

		$formIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$formIcon = new FormIcon(
				$generation,
				new FormId($result['form_id']),
				false,
				false,
				$result['image']
			);

			$formIcons[$result['form_id']] = $formIcon;
		}

		return $formIcons;
	}
}
