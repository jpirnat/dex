<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationNotFoundException;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use PDO;

final class DatabaseGenerationRepository implements GenerationRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a generation by its id.
	 *
	 * @throws GenerationNotFoundException if no generation exists with this id.
	 */
	public function getById(GenerationId $generationId) : Generation
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`icon`
			FROM `generations`
			WHERE `id` = :generation_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new GenerationNotFoundException(
				'No generation exists with id ' . $generationId->value() . '.'
			);
		}

		$generation = new Generation(
			$generationId,
			$result['identifier'],
			$result['icon']
		);

		return $generation;
	}

	/**
	 * Get a generation by its identifier
	 *
	 * @throws GenerationNotFoundException if no generation exists with this
	 *     identifier.
	 */
	public function getByIdentifier(string $identifier) : Generation
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`icon`
			FROM `generations`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new GenerationNotFoundException(
				"No generation exists with identifier $identifier."
			);
		}

		$generation = new Generation(
			new GenerationId($result['id']),
			$identifier,
			$result['icon']
		);

		return $generation;
	}

	/**
	 * Get generations that this Pokémon has appeared in (via version group forms).
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getWithPokemon(PokemonId $pokemonId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`icon`
			FROM `generations`
			WHERE `id` IN (
				SELECT
					`vg`.`generation_id`
				FROM `version_groups` AS `vg`
				INNER JOIN `version_group_forms` AS `vgf`
					ON `vg`.`id` = `vgf`.`version_group_id`
				INNER JOIN `forms` AS `f`
					ON `vgf`.`form_id` = `f`.`id`
				WHERE `f`.`pokemon_id` = :pokemon_id
			)
			ORDER BY `id`'
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$generations = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$generation = new Generation(
				new GenerationId($result['id']),
				$result['identifier'],
				$result['icon']
			);

			$generations[$result['id']] = $generation;
		}

		return $generations;
	}

	/**
	 * Get generations that this move has appeared in (via version groups).
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getWithMove(MoveId $moveId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`icon`
			FROM `generations`
			WHERE `id` IN (
				SELECT
					`vg`.`generation_id`
				FROM `version_groups` AS `vg`
				INNER JOIN `version_group_moves` AS `m`
					ON `vg`.`id` = `m`.`version_group_id`
				WHERE `m`.`move_id` = :move_id
			)
			ORDER BY `id`'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$generations = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$generation = new Generation(
				new GenerationId($result['id']),
				$result['identifier'],
				$result['icon']
			);

			$generations[$result['id']] = $generation;
		}

		return $generations;
	}

	/**
	 * Get generations since the given generation, inclusive.
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getSince(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`icon`
			FROM `generations`
			WHERE `id` >= :generation_id
			ORDER BY `id`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$generations = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$generation = new Generation(
				new GenerationId($result['id']),
				$result['identifier'],
				$result['icon']
			);

			$generations[$result['id']] = $generation;
		}

		return $generations;
	}
}
