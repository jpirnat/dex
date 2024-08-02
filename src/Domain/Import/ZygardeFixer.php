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
final readonly class ZygardeFixer
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Fix Zygarde in the usage stats.
	 */
	public function fixZygarde() : void
	{
		// All the stats tables that reference Pokémon id.
		$tables = [
			'usage_pokemon',
			'usage_rated_pokemon',

			'leads_pokemon',

			'moveset_pokemon',
		];

		// All the Zygarde forms to be converted.
		$zygardes = [
			PokemonId::ZYGARDE_50_AURA => PokemonId::ZYGARDE_50_POWER,
			PokemonId::ZYGARDE_10_AURA => PokemonId::ZYGARDE_10_POWER,
		];

		// All the formats that allow Power Construct Zygarde.
		$formatIds = [
			36, // Gen 7 AG
			37, // Gen 7 Ubers
			45, // Gen 7 Doubles Ubers
			50, // VGC 2019 Sun Series
			51, // VGC 2019 Moon Series
			52, // VGC 2019 Ultra Series
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
