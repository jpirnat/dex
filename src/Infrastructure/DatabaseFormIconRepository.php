<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\FormIcons\FormIcon;
use Jp\Dex\Domain\FormIcons\FormIconNotFoundException;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseFormIconRepository implements FormIconRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a form icon by its generation, form, gender, and direction.
	 *
	 * @param GenerationId $generationId
	 * @param FormId $formId
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     generation, form, gender, and direction.
	 *
	 * @return FormIcon
	 */
	public function getByGenerationAndFormAndFemaleAndRight(
		GenerationId $generationId,
		FormId $formId,
		bool $isFemale,
		bool $isRight
	) : FormIcon {
		$stmt = $this->db->prepare(
			'SELECT
				`image`
			FROM `form_icons`
			WHERE `generation_id` = :generation_id
				AND `form_id` = :form_id
				AND `is_female` = :is_female
				AND `is_right` = :is_right
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':form_id', $formId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', $isRight, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormIconNotFoundException(
				'No form icon exists with generation id ' . $generationId->value()
				. ', form id ' . $formId->value()
				. ', gender ' . ($isFemale ? 'female' : 'male')
				. ', and direction ' . ($isRight ? 'right' : 'left') . '.'
			);
		}

		$formIcon = new FormIcon(
			$generationId,
			$formId,
			$isFemale,
			$isRight,
			$result['image']
		);

		return $formIcon;
	}

	/**
	 * Get form icons by their generation, gender, and direction. Indexed by
	 * form id.
	 *
	 * @param GenerationId $generationId
	 * @param bool $isFemale
	 * @param bool $isRight
	 *
	 * @return FormIcon[]
	 */
	public function getByGenerationAndFemaleAndRight(
		GenerationId $generationId,
		bool $isFemale,
		bool $isRight
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`form_id`,
				`image`
			FROM `form_icons`
			WHERE `generation_id` = :generation_id
				AND `is_female` = :is_female
				AND `is_right` = :is_right'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', $isRight, PDO::PARAM_INT);
		$stmt->execute();

		$formIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$formIcon = new FormIcon(
				$generationId,
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
