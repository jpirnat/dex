<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use PDO;

class DatabaseLeadsPokemonRepository implements LeadsPokemonRepositoryInterface
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
	 * Do any leads Pokémon records exist for this year, month, and format?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function hasAny(
		int $year,
		int $month,
		FormatId $formatId
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `leads_pokemon`
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
	 * Save a leads Pokémon record.
	 *
	 * @param LeadsPokemon $leadsPokemon
	 *
	 * @return void
	 */
	public function save(LeadsPokemon $leadsPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `leads_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:pokemon_id,
				:raw,
				:raw_percent
			)'
		);
		$stmt->bindValue(':year', $leadsPokemon->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $leadsPokemon->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $leadsPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $leadsPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw', $leadsPokemon->getRaw(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $leadsPokemon->getRawPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get leads Pokémon records by year and month and format. Indexed by
	 * Pokémon id value. Use this to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return LeadsPokemon[]
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
				`raw_percent`
			FROM `leads_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$leadsPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsPokemon = new LeadsPokemon(
				$year,
				$month,
				$formatId,
				new PokemonId($result['pokemon_id']),
				$result['raw'],
				(float) $result['raw_percent']
			);

			$leadsPokemons[$result['pokemon_id']] = $leadsPokemon;
		}

		return $leadsPokemons;
	}
}
