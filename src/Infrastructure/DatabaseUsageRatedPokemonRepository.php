<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseUsageRatedPokemonRepository implements UsageRatedPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Do any usage rated Pokémon records exist for this month, format, and rating?
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `usage_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Save a usage rated Pokémon record.
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
		$stmt->bindValue(':month', $usageRatedPokemon->month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $usageRatedPokemon->formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRatedPokemon->rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $usageRatedPokemon->pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $usageRatedPokemon->rank, PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $usageRatedPokemon->usagePercent);
		$stmt->execute();
	}

	/**
	 * Get the usage rated Pokémon id for this month, format, rating, and Pokémon.
	 */
	public function getId(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : ?UsageRatedPokemonId {
		$stmt = $this->db->prepare(
			'SELECT
				`id`
			FROM `usage_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		return new UsageRatedPokemonId($result['id']);
	}
}
