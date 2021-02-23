<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use PDO;

final class DatabaseLeadsRatedPokemonRepository implements LeadsRatedPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Do any leads rated Pokémon records exist for this month, format, and
	 * rating?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `leads_rated_pokemon` AS `lrp`
				ON `urp`.`id` = `lrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a leads rated Pokémon record.
	 *
	 * @param LeadsRatedPokemon $leadsRatedPokemon
	 *
	 * @return void
	 */
	public function save(LeadsRatedPokemon $leadsRatedPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `leads_rated_pokemon` (
				`usage_rated_pokemon_id`,
				`rank`,
				`usage_percent`
			) VALUES (
				:urp_id,
				:rank,
				:usage_percent
			)'
		);
		$stmt->bindValue(':urp_id', $leadsRatedPokemon->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $leadsRatedPokemon->getRank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $leadsRatedPokemon->getUsagePercent());
		$stmt->execute();
	}
}
