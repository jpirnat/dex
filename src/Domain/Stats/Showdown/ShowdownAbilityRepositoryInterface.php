<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Showdown;

use Exception;
use Jp\Dex\Domain\Abilities\AbilityId;

interface ShowdownAbilityRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown ability name known and imported?
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownAbilityName) : bool;

	/**
	 * Is the Pokémon Showdown ability name known and ignored?
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownAbilityName) : bool;

	/**
	 * Is the Pokémon Showdown ability name known?
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownAbilityName) : bool;

	/**
	 * Add a Pokémon Showdown ability name to the list of unknown abilities.
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownAbilityName) : void;

	/**
	 * Get the ability id of a Pokémon Showdown ability name.
	 *
	 * @param string $showdownAbilityName
	 *
	 * @throws Exception if $showdownAbilityName is not an imported name.
	 *
	 * @return AbilityId
	 */
	public function getAbilityId(string $showdownAbilityName) : AbilityId;

	/**
	 * Get the names of the unknown abilities the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
