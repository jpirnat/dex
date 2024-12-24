<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseLeadsPokemonRepository implements LeadsPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Do any leads Pokémon records exist for this month and format?
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `leads_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Save a leads Pokémon record.
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
		$stmt->bindValue(':month', $leadsPokemon->month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $leadsPokemon->formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $leadsPokemon->pokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':raw', $leadsPokemon->raw, PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $leadsPokemon->rawPercent);
		$stmt->execute();
	}
}
