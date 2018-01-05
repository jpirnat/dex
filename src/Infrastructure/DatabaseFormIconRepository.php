<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\FormIcons\FormIcon;
use Jp\Dex\Domain\FormIcons\FormIconNotFoundException;
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
	 * Get a form icon by its generation, form, whether it is female, and
	 * whether it is right.
	 *
	 * @param Generation $generation
	 * @param FormId $formId
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     generation, form, female-ness, and right-ness.
	 *
	 * @return FormIcon
	 */
	public function getByGenerationAndFormAndFemaleAndRight(
		Generation $generation,
		FormId $formId,
		bool $isFemale,
		bool $isRight
	) : FormIcon {
		$stmt = $this->db->prepare(
			'SELECT
				`image`
			FROM `form_icons`
			WHERE `generation` = :generation
				AND `form_id` = :form_id
				AND `is_female` = :is_female
				AND `is_right` = :is_right'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':form_id', $formId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', $isRight, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormIconNotFoundException(
				'No form icon exists with generation ' . $generation->getValue()
				. ', form id ' . $formId->value()
				. ', female-ness ' . ($isFemale ? 'true' : 'false')
				. ', and right-ness ' . ($isRight ? 'true' : 'false') . '.'
			);
		}

		$formIcon = new FormIcon(
			$generation,
			$formId,
			$isFemale,
			$isRight,
			$result['image']
		);

		return $formIcon;
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
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', $isRight, PDO::PARAM_INT);
		$stmt->execute();

		$formIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$formIcon = new FormIcon(
				$generation,
				new FormId($result['form_id']),
				$isFemale,
				$isRight,
				$result['image']
			);

			$formIcons[$result['form_id']] = $formIcon;
		}

		return $formIcons;
	}
}
