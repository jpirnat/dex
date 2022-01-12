<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Abilities\AbilityId;

interface ShowdownAbilityRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown ability name known and imported?
	 */
	public function isImported(string $showdownAbilityName) : bool;

	/**
	 * Is the Pokémon Showdown ability name known and ignored?
	 */
	public function isIgnored(string $showdownAbilityName) : bool;

	/**
	 * Is the Pokémon Showdown ability name known?
	 */
	public function isKnown(string $showdownAbilityName) : bool;

	/**
	 * Add a Pokémon Showdown ability name to the list of unknown abilities.
	 */
	public function addUnknown(string $showdownAbilityName) : void;

	/**
	 * Get the ability id of a Pokémon Showdown ability name.
	 *
	 * @throws AbilityNotImportedException if $showdownAbilityName is not an
	 *     imported ability name.
	 */
	public function getAbilityId(string $showdownAbilityName) : AbilityId;

	/**
	 * Get the names of the unknown abilities the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
