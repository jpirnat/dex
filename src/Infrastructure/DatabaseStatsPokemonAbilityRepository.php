<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Abilities\StatsPokemonAbility;
use Jp\Dex\Domain\Abilities\StatsPokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

final class DatabaseStatsPokemonAbilityRepository implements StatsPokemonAbilityRepositoryInterface
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
	 * Get stats Pokémon abilities by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return StatsPokemonAbility[] Ordered by percent descending.
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
				`a`.`identifier`,
				`an`.`name`,
				`mra`.`percent`,
				`mrap`.`percent` AS `prev_percent`
			FROM `moveset_rated_abilities` AS `mra`
			INNER JOIN `abilities` AS `a`
				ON `mra`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `mra`.`ability_id` = `an`.`ability_id`
			LEFT JOIN `moveset_rated_abilities` AS `mrap`
				ON `mrap`.`month` = :prev_month
				AND `mra`.`format_id` = `mrap`.`format_id`
				AND `mra`.`rating` = `mrap`.`rating`
				AND `mra`.`pokemon_id` = `mrap`.`pokemon_id`
				AND `mra`.`ability_id` = `mrap`.`ability_id`
			WHERE `mra`.`month` = :month
				AND `mra`.`format_id` = :format_id
				AND `mra`.`rating` = :rating
				AND `mra`.`pokemon_id` = :pokemon_id
				AND `an`.`language_id` = :language_id
			ORDER BY `mra`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':prev_month', $prevMonth, PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ability = new StatsPokemonAbility(
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent']
			);

			$abilities[] = $ability;
		}

		return $abilities;
	}
}
