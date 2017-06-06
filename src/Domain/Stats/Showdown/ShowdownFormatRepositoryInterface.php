<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Showdown;

use Jp\Dex\Domain\Formats\FormatId;

interface ShowdownFormatRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown format name known and imported?
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isImported(int $year, int $month, string $showdownFormatName) : bool;

	/**
	 * Is the Pokémon Showdown format name known and ignored?
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isIgnored(int $year, int $month, string $showdownFormatName) : bool;

	/**
	 * Is the Pokémon Showdown format name known?
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isKnown(int $year, int $month, string $showdownFormatName) : bool;

	/**
	 * Add a Pokémon Showdown format name to the list of unknown formats.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return void
	 */
	public function addUnknown(int $year, int $month, string $showdownFormatName) : void;

	/**
	 * Get the format id of a Pokémon Showdown format name.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @throws FormatNotImportedException if $showdownFormatName is not an
	 *     imported format name.
	 *
	 * @return FormatId
	 */
	public function getFormatId(int $year, int $month, string $showdownFormatName) : FormatId;

	/**
	 * Get the names of the unknown formats the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
