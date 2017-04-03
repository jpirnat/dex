<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownFormatRepository
{
	/**
	 * Indexed by year, then month, then Showdown format name.
	 * The value is the format id.
	 *
	 * @var int[][][] $formatsToImport
	 */
	protected $formatsToImport = [];

	/**
	 * Indexed by year, then month, then Showdown format name.
	 * The value is the format id, or null.
	 *
	 * @var ?int[][][]
	 */
	protected $formatsToIgnore = [];

	/**
	 * Indexed by year, then month, then Showdown format name.
	 * The value is the Showdown format name.
	 *
	 * @var string[][][]
	 */
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
				`year`,
				`month`,
				`name`,
				`format_id`
			FROM `showdown_formats_to_import`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->formatsToImport
				[$result['year']]
				[$result['month']]
				[$result['name']]
			= $result['format_id'];
		}

		$stmt = $db->prepare(
			'SELECT
				`year`,
				`month`,
				`name`,
				`format_id`
			FROM `showdown_formats_to_ignore`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->formatsToIgnore
				[$result['year']]
				[$result['month']]
				[$result['name']]
			= $result['format_id'];
		}
	}

	/**
	 * Is the Pokémon Showdown format name known and imported?
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isImported(int $year, int $month, string $showdownFormatName) : bool
	{
		return isset($this->formatsToImport[$year][$month][$showdownFormatName]);
	}

	/**
	 * Is the Pokémon Showdown format name known and ignored?
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isIgnored(int $year, int $month, string $showdownFormatName) : bool
	{
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return
			isset($this->formatsToIgnore[$year][$month])
			&& array_key_exists($showdownFormatName, $this->formatsToIgnore[$year][$month])
		;
	}

	/**
	 * Is the Pokémon Showdown format name known?
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isKnown(int $year, int $month, string $showdownFormatName) : bool
	{
		return $this->isImported($year, $month, $showdownFormatName)
			|| $this->isIgnored($year, $month, $showdownFormatName)
		;
	}

	/**
	 * Add a Pokémon Showdown format name to the list of unknown formats.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @return void
	 */
	public function addUnknown(int $year, int $month, string $showdownFormatName) : void
	{
		$this->unknownFormats[$year][$month][$showdownFormatName] = $showdownFormatName;
	}

	/**
	 * Get the format id of a Pokémon Showdown format name.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $showdownFormatName
	 *
	 * @throws Exception if $showdownFormatName is not an imported name.
	 *
	 * @return int
	 */
	public function getFormatId(int $year, int $month, string $showdownFormatName) : int
	{
		// If the format is imported, return the format id.
		if ($this->isImported($year, $month, $showdownFormatName)) {
			return $this->formatsToImport[$year][$month][$showdownFormatName];
		}

		// If the format is not known, add it to the list of unknown formats.
		if (!$this->isKnown($year, $month, $showdownFormatName)) {
			$this->addUnknown($year, $month, $showdownFormatName);
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
