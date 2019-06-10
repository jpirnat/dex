<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
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
	 * Do any leads Pokémon records exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `leads_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
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
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`
			) VALUES (
				:month,
				:format_id,
				:pokemon_id,
				:raw,
				:raw_percent
			)'
		);
		$stmt->bindValue(':month', $leadsPokemon->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $leadsPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $leadsPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw', $leadsPokemon->getRaw(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $leadsPokemon->getRawPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
