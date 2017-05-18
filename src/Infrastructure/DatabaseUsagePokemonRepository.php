<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\UsagePokemon;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use PDO;

class DatabaseUsagePokemonRepository implements UsagePokemonRepositoryInterface
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
	 * Do any usage Pokémon records exist for this year, month, and format?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a usage Pokémon record.
	 *
	 * @param UsagePokemon $usagePokemon
	 *
	 * @return void
	 */
	public function save(UsagePokemon $usagePokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`,
				`real`,
				`real_percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:pokemon_id,
				:raw,
				:raw_percent,
				:real,
				:real_percent
			)'
		);
		$stmt->bindValue(':year', $usagePokemon->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $usagePokemon->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $usagePokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $usagePokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw', $usagePokemon->getRaw(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $usagePokemon->getRawPercent(), PDO::PARAM_STR);
		$stmt->bindValue(':real', $usagePokemon->getReal(), PDO::PARAM_INT);
		$stmt->bindValue(':reap_percent', $usagePokemon->getRealPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get usage Pokémon records by year and month and format. Indexed by
	 * Pokémon id value. Use this to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return UsagePokemon[]
	 */
	public function getByYearAndMonthAndFormat(
		int $year,
		int $month,
		FormatId $formatId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`raw`,
				`raw_percent`,
				`real`,
				`real_percent`
			FROM `usage_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usagePokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usagePokemon = new UsagePokemon(
				$year,
				$month,
				$formatId,
				new PokemonId($result['pokemon_id']),
				$result['raw'],
				(float) $result['raw_percent'],
				$result['real'],
				(float) $result['real_percent']
			);

			$usagePokemons[$result['pokemon_id']] = $usagePokemon;
		}

		return $usagePokemons;
	}
}
