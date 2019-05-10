<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Natures\NatureId;

interface ShowdownNatureRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown nature name known and imported?
	 *
	 * @param string $showdownNatureName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownNatureName) : bool;

	/**
	 * Is the Pokémon Showdown nature name known and ignored?
	 *
	 * @param string $showdownNatureName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownNatureName) : bool;

	/**
	 * Is the Pokémon Showdown nature name known?
	 *
	 * @param string $showdownNatureName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownNatureName) : bool;

	/**
	 * Add a Pokémon Showdown nature name to the list of unknown natures.
	 *
	 * @param string $showdownNatureName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownNatureName) : void;

	/**
	 * Get the nature id of a Pokémon Showdown nature name.
	 *
	 * @param string $showdownNatureName
	 *
	 * @throws NatureNotImportedException if $showdownNatureName is not an
	 *     imported nature name.
	 *
	 * @return NatureId
	 */
	public function getNatureId(string $showdownNatureName) : NatureId;

	/**
	 * Get the names of the unknown natures the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
