<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Spreads\StatsPokemonSpreadRepositoryInterface;
use Jp\Dex\Domain\Spreads\StatsPokemonSpread;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use PDO;

final class DatabaseStatsPokemonSpreadRepository implements StatsPokemonSpreadRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon spreads by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonSpread[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`nn`.`name`,
				`n`.`increased_stat_id`,
				`n`.`decreased_stat_id`,
				`s`.`hp`,
				`s`.`atk`,
				`s`.`def`,
				`s`.`spa`,
				`s`.`spd`,
				`s`.`spe`,
				`s`.`percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_spreads` AS `s`
				ON `urp`.`id` = `s`.`usage_rated_pokemon_id`
			INNER JOIN `natures` AS `n`
				ON `s`.`nature_id` = `n`.`id`
			INNER JOIN `nature_names` AS `nn`
				ON `n`.`id` = `nn`.`nature_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `nn`.`language_id` = :language_id
			ORDER BY `s`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$spreads = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$spread = new StatsPokemonSpread(
				$result['name'],
				$result['increased_stat_id'] ? new StatId($result['increased_stat_id']) : null,
				$result['decreased_stat_id'] ? new StatId($result['decreased_stat_id']) : null,
				new StatValueContainer([
					new StatValue(new StatId(StatId::HP), $result['hp']),
					new StatValue(new StatId(StatId::ATTACK), $result['atk']),
					new StatValue(new StatId(StatId::DEFENSE), $result['def']),
					new StatValue(new StatId(StatId::SPECIAL_ATTACK), $result['spa']),
					new StatValue(new StatId(StatId::SPECIAL_DEFENSE), $result['spd']),
					new StatValue(new StatId(StatId::SPEED), $result['spe']),
				]),
				(float) $result['percent']
			);

			$spreads[] = $spread;
		}

		return $spreads;
	}
}
