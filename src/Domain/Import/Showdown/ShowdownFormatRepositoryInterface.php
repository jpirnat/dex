<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;

interface ShowdownFormatRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown format name known and imported?
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isImported(DateTime $month, string $showdownFormatName) : bool;

	/**
	 * Is the Pokémon Showdown format name known and ignored?
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isIgnored(DateTime $month, string $showdownFormatName) : bool;

	/**
	 * Is the Pokémon Showdown format name known?
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isKnown(DateTime $month, string $showdownFormatName) : bool;

	/**
	 * Add a Pokémon Showdown format name to the list of unknown formats.
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return void
	 */
	public function addUnknown(DateTime $month, string $showdownFormatName) : void;

	/**
	 * Get the format id of a Pokémon Showdown format name.
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @throws FormatNotImportedException if $showdownFormatName is not an
	 *     imported format name.
	 *
	 * @return FormatId
	 */
	public function getFormatId(DateTime $month, string $showdownFormatName) : FormatId;

	/**
	 * Get the names of the unknown formats the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
