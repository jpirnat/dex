<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpread;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use PDO;

class DatabaseMovesetRatedSpreadRepository implements MovesetRatedSpreadRepositoryInterface
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
	 * Save a moveset rated spread record.
	 *
	 * @param MovesetRatedSpread $movesetRatedSpread
	 *
	 * @return void
	 */
	public function save(MovesetRatedSpread $movesetRatedSpread) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_spreads` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`nature_id`,
				`hp`,
				`atk`,
				`def`,
				`spa`,
				`spd`,
				`spe`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:nature_id,
				:hp,
				:atk,
				:def,
				:spa,
				:spd,
				:spe,
				:percent
			)'
		);
		$stmt->bindValue(':year', $movesetRatedSpread->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedSpread->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedSpread->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedSpread->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedSpread->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':nature_id', $movesetRatedSpread->getNatureId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':hp', $movesetRatedSpread->getHpEvs(), PDO::PARAM_INT);
		$stmt->bindValue(':atk', $movesetRatedSpread->getAttackEvs(), PDO::PARAM_INT);
		$stmt->bindValue(':def', $movesetRatedSpread->getDefenseEvs(), PDO::PARAM_INT);
		$stmt->bindValue(':spa', $movesetRatedSpread->getSpecialAttackEvs(), PDO::PARAM_INT);
		$stmt->bindValue(':spd', $movesetRatedSpread->getSpecialDefenseEvs(), PDO::PARAM_INT);
		$stmt->bindValue(':spe', $movesetRatedSpread->getSpeedEvs(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedSpread->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get moveset rated spread records by year, month, format, rating, and
	 * Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedSpread[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`nature_id`,
				`hp`,
				`atk`,
				`def`,
				`spa`,
				`spd`,
				`spe`,
				`percent`
			FROM `moveset_rated_spreads`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedSpreads = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedSpreads[] = new MovesetRatedSpread(
				$year,
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new NatureId($result['nature_id']),
				$result['hp'],
				$result['atk'],
				$result['def'],
				$result['spa'],
				$result['spd'],
				$result['spe'],
				(float) $result['percent']
			);
		}

		return $movesetRatedSpreads;
	}
}
