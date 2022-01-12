<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Natures\NatureId;

interface ShowdownNatureRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown nature name known and imported?
	 */
	public function isImported(string $showdownNatureName) : bool;

	/**
	 * Is the Pokémon Showdown nature name known and ignored?
	 */
	public function isIgnored(string $showdownNatureName) : bool;

	/**
	 * Is the Pokémon Showdown nature name known?
	 */
	public function isKnown(string $showdownNatureName) : bool;

	/**
	 * Add a Pokémon Showdown nature name to the list of unknown natures.
	 */
	public function addUnknown(string $showdownNatureName) : void;

	/**
	 * Get the nature id of a Pokémon Showdown nature name.
	 *
	 * @throws NatureNotImportedException if $showdownNatureName is not an
	 *     imported nature name.
	 */
	public function getNatureId(string $showdownNatureName) : NatureId;

	/**
	 * Get the names of the unknown natures the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
