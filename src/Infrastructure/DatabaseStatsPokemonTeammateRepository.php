<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Teammates\StatsPokemonTeammate;
use Jp\Dex\Domain\Teammates\StatsPokemonTeammateRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseStatsPokemonTeammateRepository implements StatsPokemonTeammateRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon teammates by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsPokemonTeammate[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`mrt`.`percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_teammates` AS `mrt`
				ON `urp`.`id` = `mrt`.`usage_rated_pokemon_id`
			INNER JOIN `form_icons` AS `fi`
				ON `mrt`.`teammate_id` = `fi`.`form_id`
			INNER JOIN `pokemon` AS `p`
				ON `mrt`.`teammate_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `mrt`.`teammate_id` = `pn`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `fi`.`generation_id` = :generation_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			ORDER BY `mrt`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$teammates = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$teammate = new StatsPokemonTeammate(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				(float) $result['percent']
			);

			$teammates[] = $teammate;
		}

		return $teammates;
	}
}
