<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use PDO;

final class DatabaseUsageRatedPokemonRepository implements UsageRatedPokemonRepositoryInterface
{
	private PDO $db;

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
	 * Do any usage rated Pokémon records exist for this month, format, and
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
			FROM `usage_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a usage rated Pokémon record.
	 *
	 * @param UsageRatedPokemon $usageRatedPokemon
	 *
	 * @return void
	 */
	public function save(UsageRatedPokemon $usageRatedPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_rated_pokemon` (
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`rank`,
				`usage_percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:rank,
				:usage_percent
			)'
		);
		$stmt->bindValue(':month', $usageRatedPokemon->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $usageRatedPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRatedPokemon->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $usageRatedPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $usageRatedPokemon->getRank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $usageRatedPokemon->getUsagePercent(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
