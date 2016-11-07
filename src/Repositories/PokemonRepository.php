<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories;

use PDO;

class PokemonRepository
{
	/** @var PDO $db */
	protected $db;

	/** @var array $pokemonIds */
	protected $pokemonIds;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;

		$stmt = $this->db->prepare(
			'SELECT
				`name`,
				`id`
			FROM `pokemon`'
		);
		$stmt->execute();
		$this->pokemonIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Insert a pokemon into the database.
	 *
	 * @param string $name
	 *
	 * @return int The pokemon's id
	 */
	protected function insertPokemon(string $name) : int
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `pokemon` (
				`name`
			) VALUES (
				:name
			)'
		);
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->execute();
		return (int) $this->db->lastInsertId();
	}

	/**
	 * Get the id of a pokemon name.
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function getPokemonId(string $name) : int
	{
		if (!isset($this->pokemonIds[$name])) {
			$this->pokemonIds[$name] = $this->insertPokemon($name);
		}

		return $this->pokemonIds[$name];
	}
}
