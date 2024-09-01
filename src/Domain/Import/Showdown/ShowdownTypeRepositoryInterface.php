<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Types\TypeId;

interface ShowdownTypeRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown type name known and imported?
	 */
	public function isImported(string $showdownTypeName) : bool;

	/**
	 * Is the Pokémon Showdown type name known and ignored?
	 */
	public function isIgnored(string $showdownTypeName) : bool;

	/**
	 * Is the Pokémon Showdown type name known?
	 */
	public function isKnown(string $showdownTypeName) : bool;

	/**
	 * Add a Pokémon Showdown type name to the list of unknown types.
	 */
	public function addUnknown(string $showdownTypeName) : void;

	/**
	 * Get the type id of a Pokémon Showdown type name.
	 *
	 * @throws TypeNotImportedException if $showdownTypeName is not an
	 *     imported type name.
	 */
	public function getTypeId(string $showdownTypeName) : TypeId;

	/**
	 * Get the names of the unknown types the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
