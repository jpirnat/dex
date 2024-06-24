<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import;

use DateTime;
use PDO;

/**
 * Starting with April 2021, the teammates usage percent is calculated differently.
 *
 * The old formula is P(X|Y) - P(X), aka the probability that teams with Pokémon Y
 * will also have Teammate X, minus the overall probability that the team will have X.
 *
 * The new formula is P(X|Y), aka the probability that teams with Pokémon Y
 * will also have Teammate X.
 *
 * Going forward, Porydex is converting the pre-April 2021 teammates data to
 * the new formula. This is done simply by adding the teammate's overall Usage %
 * into the Teammate %.
 */
final readonly class TeammatesFixer
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Fix Teammates in the usage stats.
	 */
	public function fixTeammates(DateTime $month) : void
	{
		if ($month >= new DateTime('2021-04-01')) {
			return;
		}

		$stmt = $this->db->prepare(
			'UPDATE
				`moveset_rated_teammates` AS `mrt`
			INNER JOIN `usage_rated_pokemon` AS `urp`
				ON `mrt`.`usage_rated_pokemon_id` = `urp`.`id`
			INNER JOIN `usage_rated_pokemon` AS `urt`
				ON `urt`.`month` = `urp`.`month`
				AND `urt`.`format_id` = `urp`.`format_id`
				AND `urt`.`rating` = `urp`.`rating`
				AND `urt`.`pokemon_id` = `mrt`.`teammate_id`
			SET
				`mrt`.`percent` = LEAST(`mrt`.`percent` + `urt`.`usage_percent`, 100)
			WHERE `urp`.`month` = :month'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->execute();

		// We need to clamp the new value to at most 100 because rounding errors
		// occasionally bring it slightly above 100, and other rarer errors
		// bring it far above 100.
	}
}
