<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Forms\Form;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Forms\FormNotFoundException;
use Jp\Dex\Domain\Forms\FormRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseFormRepository implements FormRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a form by its id.
	 *
	 * @throws FormNotFoundException if no form exists with this id.
	 */
	public function getById(FormId $formId) : Form
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`pokemon_id`
			FROM `forms`
			WHERE `id` = :form_id
			LIMIT 1'
		);
		$stmt->bindValue(':form_id', $formId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new FormNotFoundException(
				'No form exists with id ' . $formId->value() . '.'
			);
		}

		return new Form(
			$formId,
			$result['identifier'],
			new PokemonId($result['pokemon_id']),
		);
	}

	/**
	 * Get form ids of this Pokémon, available in this version group.
	 * *
	 * * @return FormId[] Indexed by id.
	 */
	public function getByVgAndPokemon(VersionGroupId $versionGroupId, PokemonId $pokemonId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`
			FROM `forms`
			WHERE `id` IN (
				SELECT
					`form_id`
				FROM `version_group_forms`
				WHERE `version_group_id` = :version_group_id
			)
			AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$formIds = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$formId = new FormId($result['id']);

			$formIds[$result['id']] = $formId;
		}

		return $formIds;
	}
}
