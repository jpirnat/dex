<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Models\Model;
use Jp\Dex\Domain\Models\ModelNotFoundException;
use Jp\Dex\Domain\Models\ModelRepositoryInterface;
use PDO;

final readonly class DatabaseModelRepository implements ModelRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a model by its form, shininess, direction, gender, and attacking
	 * index.
	 *
	 * @throws ModelNotFoundException if no model exists with this form,
	 *     shininess, direction, gender, and attacking index.
	 */
	public function getByFormAndShinyAndBackAndFemaleAndAttackingIndex(
		FormId $formId,
		bool $isShiny,
		bool $isBack,
		bool $isFemale,
		int $attackingIndex,
	) : Model {
		$stmt = $this->db->prepare(
			'SELECT
				`image`
			FROM `models`
			WHERE `form_id` = :form_id
				AND `is_shiny` = :is_shiny
				AND `is_back` = :is_back
				AND `is_female` = :is_female
				AND `attacking_index` = :attacking_index
			LIMIT 1'
		);
		$stmt->bindValue(':form_id', $formId->value, PDO::PARAM_INT);
		$stmt->bindValue(':is_shiny', $isShiny, PDO::PARAM_INT);
		$stmt->bindValue(':is_back', $isBack, PDO::PARAM_INT);
		$stmt->bindValue(':is_female', $isFemale, PDO::PARAM_INT);
		$stmt->bindValue(':attacking_index', $attackingIndex, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ModelNotFoundException(
				'No model exists with form id ' . $formId->value
				. ', shininess ' . ($isShiny ? 'shiny' : 'normal')
				. ', direction ' . ($isBack ? 'front' : 'back')
				. ', gender ' . ($isFemale ? 'female' : 'male')
				. ', and attacking index ' . $attackingIndex . '.'
			);
		}

		return new Model(
			$formId,
			$isShiny,
			$isBack,
			$isFemale,
			$attackingIndex,
			$result['image'],
		);
	}
}
