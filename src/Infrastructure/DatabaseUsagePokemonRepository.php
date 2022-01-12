<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Usage\UsagePokemon;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use PDO;

final class DatabaseUsagePokemonRepository implements UsagePokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Do any usage Pokémon records exist for this month and format?
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a usage Pokémon record.
	 */
	public function save(UsagePokemon $usagePokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_pokemon` (
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`,
				`real`,
				`real_percent`
			) VALUES (
				:month,
				:format_id,
				:pokemon_id,
				:raw,
				:raw_percent,
				:real,
				:real_percent
			)'
		);
		$stmt->bindValue(':month', $usagePokemon->getMonth()->format('Y-m-01'));
		$stmt->bindValue(':format_id', $usagePokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $usagePokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw', $usagePokemon->getRaw(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $usagePokemon->getRawPercent());
		$stmt->bindValue(':real', $usagePokemon->getReal(), PDO::PARAM_INT);
		$stmt->bindValue(':real_percent', $usagePokemon->getRealPercent());
		$stmt->execute();
	}
}
