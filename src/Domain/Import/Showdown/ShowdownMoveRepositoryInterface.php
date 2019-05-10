<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Moves\MoveId;

interface ShowdownMoveRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown move name known and imported?
	 *
	 * @param string $showdownMoveName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownMoveName) : bool;

	/**
	 * Is the Pokémon Showdown move name known and ignored?
	 *
	 * @param string $showdownMoveName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownMoveName) : bool;

	/**
	 * Is the Pokémon Showdown move name known?
	 *
	 * @param string $showdownMoveName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownMoveName) : bool;

	/**
	 * Add a Pokémon Showdown move name to the list of unknown moves.
	 *
	 * @param string $showdownMoveName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownMoveName) : void;

	/**
	 * Get the move id of a Pokémon Showdown move name.
	 *
	 * @param string $showdownMoveName
	 *
	 * @throws MoveNotImportedException if $showdownMoveName is not an imported
	 *     move name.
	 *
	 * @return MoveId
	 */
	public function getMoveId(string $showdownMoveName) : MoveId;

	/**
	 * Get the names of the unknown moves the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
