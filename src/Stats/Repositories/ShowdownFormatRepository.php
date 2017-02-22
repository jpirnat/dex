<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownFormatRepository
{
	/** @var int[] $formatsToImport */
	protected $formatsToImport = [];

	/** @var ?int[] $formatsToIgnore */
	protected $formatsToIgnore = [];

	/** @var string[] $unknownFormats */
	protected $unknownFormats = [];

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$stmt = $db->prepare(
			'SELECT
				`name`,
				`format_id`
			FROM `showdown_formats_to_import`'
		);
		$stmt->execute();
		$this->formatsToImport = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`format_id`
			FROM `showdown_formats_to_ignore`'
		);
		$stmt->execute();
		$this->formatsToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown format name known and imported?
	 *
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownFormatName) : bool
	{
		return isset($this->formatsToImport[$showdownFormatName]);
	}

	/**
	 * Is the Pokémon Showdown format name known and ignored?
	 *
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownFormatName) : bool
	{
		return isset($this->formatsToIgnore[$showdownFormatName]);
	}

	/**
	 * Is the Pokémon Showdown format name known?
	 *
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownFormatName) : bool
	{
		return isset($this->formatsToImport[$showdownFormatName])
			|| isset($this->formatsToIgnore[$showdownFormatName])
		;
	}

	/**
	 * Add a Pokémon Showdown format name to the list of unknown formats.
	 *
	 * @param string $showdownFormatName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownFormatName) : void
	{
		$this->unknownFormats[$showdownFormatName] = $showdownFormatName;
	}

	/**
	 * Get the format id of a Pokémon Showdown format name.
	 *
	 * @param string $showdownFormatName
	 *
	 * @throws Exception if $showdownFormatName is not an imported name.
	 *
	 * @return int
	 */
	public function getFormatId(string $showdownFormatName) : int
	{
		// If the format is imported, return the format id.
		if ($this->isImported($showdownFormatName)) {
			return $this->formatsToImport[$showdownFormatName];
		}

		// If the format is not known, add it to the list of unknown formats.
		if (!$this->isKnown($showdownFormatName)) {
			$this->addUnknown($showdownFormatName);
		}

		throw new Exception('Format should not be imported: ' . $showdownFormatName);
	}

	/**
	 * Get the names of the unknown formats the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownFormats;
	}
}
