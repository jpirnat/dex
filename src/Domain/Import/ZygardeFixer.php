<?php
/** @noinspection SqlResolve */
declare(strict_types=1);

namespace Jp\Dex\Domain\Import;

use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

/**
 * The Smogon Pokémon Showdown usage stats do not differentiate between Aura
 * Break Zygarde and Power Construct Zygarde. So, all Zygardes are imported to
 * Porydex as the Aura Break forms. This class converts Zygarde in the usage
 * stats to the Power Construct forms for the formats in which they are allowed.
 */
class ZygardeFixer
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
	 * Fix Zygarde in the usage stats.
	 *
	 * @return void
	 */
	public function fixZygarde() : void
	{
		// All the stats tables that reference Pokémon id.
		$tables = [
			'usage_pokemon',
			'usage_rated_pokemon',

			'leads_pokemon',
			'leads_rated_pokemon',

			'moveset_pokemon',
			'moveset_rated_abilities',
			'moveset_rated_counters',
			'moveset_rated_items',
			'moveset_rated_moves',
			'moveset_rated_pokemon',
			'moveset_rated_spreads',
			'moveset_rated_teammates',
		];

		// All the Zygarde forms to be converted.
		$zygardes = [
			PokemonId::ZYGARDE_50_AURA => PokemonId::ZYGARDE_50_POWER,
			PokemonId::ZYGARDE_10_AURA => PokemonId::ZYGARDE_10_POWER,
		];

		// All the formats that allow Power Construct Zygarde.
		$formatIds = [
			15, // Gen 7 Anything Goes
			16, // Gen 7 Ubers
			22, // Gen 7 Doubles Ubers
			26, // VGC 2019 Sun Series
			27, // VGC 2019 Moon Series
			28, // VGC 2019 Ultra Series
		];

		foreach ($formatIds as $formatId) {
			foreach ($zygardes as $zygardeAura => $zygardePower) {
				foreach ($tables as $table) {
					$stmt = $this->db->prepare(
						"UPDATE `$table` SET
							`pokemon_id` = :zygarde_power
						WHERE `format_id` = :format_id
							AND `pokemon_id` = :zygarde_aura"
					);
					$stmt->bindValue(':zygarde_power', $zygardePower, PDO::PARAM_INT);
					$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
					$stmt->bindValue(':zygarde_aura', $zygardeAura, PDO::PARAM_INT);
					$stmt->execute();
				}
			}
		}
	}
}
