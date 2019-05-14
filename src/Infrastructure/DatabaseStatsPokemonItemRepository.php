<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\StatsPokemonItem;
use Jp\Dex\Domain\Items\StatsPokemonItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

class DatabaseStatsPokemonItemRepository implements StatsPokemonItemRepositoryInterface
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
	 * Get stats Pokémon items by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return StatsPokemonItem[]
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
				`i`.`identifier`,
				`in`.`name`,
				`mri`.`percent`,
				`mrip`.`percent` AS `prev_percent`
			FROM `moveset_rated_items` AS `mri`
			INNER JOIN `items` AS `i`
				ON `mri`.`item_id` = `i`.`id`
			INNER JOIN `item_names` AS `in`
				ON `mri`.`item_id` = `in`.`item_id`
			LEFT JOIN `moveset_rated_items` AS `mrip`
				ON `mrip`.`month` = :prev_month
				AND `mri`.`format_id` = `mrip`.`format_id`
				AND `mri`.`rating` = `mrip`.`rating`
				AND `mri`.`pokemon_id` = `mrip`.`pokemon_id`
				AND `mri`.`item_id` = `mrip`.`item_id`
			WHERE `mri`.`month` = :month
				AND `mri`.`format_id` = :format_id
				AND `mri`.`rating` = :rating
				AND `mri`.`pokemon_id` = :pokemon_id
				AND `in`.`language_id` = :language_id
			ORDER BY `mri`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':prev_month', $prevMonth, PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$items = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = new StatsPokemonItem(
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent']
			);

			$items[] = $item;
		}

		return $items;
	}
}
