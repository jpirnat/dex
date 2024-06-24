<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\FormIcons\FormIcon;
use Jp\Dex\Domain\FormIcons\FormIconNotFoundException;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseFormIconRepository implements FormIconRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a form icon by its version group, form, gender, and direction.
	 *
	 * @throws FormIconNotFoundException if no form icon exists with this
	 *     version group, form, gender, and direction.
	 */
	public function getByVgAndFormAndFemaleAndRight(
		VersionGroupId $versionGroupId,
		FormId $formId,
		bool $isFemale,
		bool $isRight,
	) : FormIcon {
		$stmt = $this->db->prepare(
			'SELECT
				`image`
			FROM `form_icons`
			WHERE `version_group_id` = :version_group_id
				AND `form_id` = :form_id
				AND `is_female` = :is_female
				AND `is_right` = :is_right
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':form_id', $formId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', $isRight, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormIconNotFoundException(
				'No form icon exists with version group id ' . $versionGroupId->value()
				. ', form id ' . $formId->value()
				. ', gender ' . ($isFemale ? 'female' : 'male')
				. ', and direction ' . ($isRight ? 'right' : 'left') . '.'
			);
		}

		return new FormIcon(
			$versionGroupId,
			$formId,
			$isFemale,
			$isRight,
			$result['image'],
		);
	}

	/**
	 * Get form icons by their version group, gender, and direction.
	 *
	 * @return FormIcon[] Indexed by form id.
	 */
	public function getByVgAndFemaleAndRight(
		VersionGroupId $versionGroupId,
		bool $isFemale,
		bool $isRight,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`form_id`,
				`image`
			FROM `form_icons`
			WHERE `version_group_id` = :version_group_id
				AND `is_female` = :is_female
				AND `is_right` = :is_right'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':is_right', $isRight, PDO::PARAM_INT);
		$stmt->execute();

		$formIcons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$formIcon = new FormIcon(
				$versionGroupId,
				new FormId($result['form_id']),
				$isFemale,
				$isRight,
				$result['image'],
			);

			$formIcons[$result['form_id']] = $formIcon;
		}

		return $formIcons;
	}
}
