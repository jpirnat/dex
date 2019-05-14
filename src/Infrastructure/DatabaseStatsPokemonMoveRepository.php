<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\StatsPokemonMove;
use Jp\Dex\Domain\Moves\StatsPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

class DatabaseStatsPokemonMoveRepository implements StatsPokemonMoveRepositoryInterface
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
	 * Get stats Pokémon moves by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return StatsPokemonMove[]
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`mrm`.`percent`,
				`mrmp`.`percent` AS `prev_percent`
			FROM `moveset_rated_moves` AS `mrm`
			INNER JOIN `moves` AS `m`
				ON `mrm`.`move_id` = `m`.`id`
			INNER JOIN `move_names` AS `mn`
				ON `mrm`.`move_id` = `mn`.`move_id`
			LEFT JOIN `moveset_rated_moves` AS `mrmp`
				ON `mrmp`.`month` = :prev_month
				AND `mrm`.`format_id` = `mrmp`.`format_id`
				AND `mrm`.`rating` = `mrmp`.`rating`
				AND `mrm`.`pokemon_id` = `mrmp`.`pokemon_id`
				AND `mrm`.`move_id` = `mrmp`.`move_id`
			WHERE `mrm`.`month` = :month
				AND `mrm`.`format_id` = :format_id
				AND `mrm`.`rating` = :rating
				AND `mrm`.`pokemon_id` = :pokemon_id
				AND `mn`.`language_id` = :language_id
			ORDER BY `mrm`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':prev_month', $prevMonth, PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$moves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$move = new StatsPokemonMove(
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent']
			);

			$moves[] = $move;
		}

		return $moves;
	}
}
