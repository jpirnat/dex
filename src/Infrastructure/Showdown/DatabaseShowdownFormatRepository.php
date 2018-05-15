<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Showdown\FormatNotImportedException;
use Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface;
use PDO;

class DatabaseShowdownFormatRepository implements ShowdownFormatRepositoryInterface
{
	/**
	 * Indexed by month, then Showdown format name.
	 * The value is the format id.
	 *
	 * @var FormatId[][] $formatsToImport
	 */
	private $formatsToImport = [];

	/**
	 * Indexed by month, then Showdown format name.
	 * The value is the format id, or null.
	 *
	 * @var ?FormatId[][]
	 */
	private $formatsToIgnore = [];

	/**
	 * Indexed by month, then Showdown format name.
	 * The value is the Showdown format name.
	 *
	 * @var string[][]
	 */
	private $unknownFormats = [];

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$stmt = $db->prepare(
			'SELECT
				`month`,
				`name`,
				`format_id`
			FROM `showdown_formats_to_import`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->formatsToImport
				[$result['month']]
				[$result['name']]
			= new FormatId($result['format_id']);
		}

		$stmt = $db->prepare(
			'SELECT
				`month`,
				`name`,
				`format_id`
			FROM `showdown_formats_to_ignore`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($result['format_id'] !== null) {
				// The Pokémon Showdown format name has a format id.
				$formatId = new FormatId($result['format_id']);
			} else {
				$formatId = null;
			}

			$this->formatsToIgnore
				[$result['month']]
				[$result['name']]
			= $formatId;
		}
	}

	/**
	 * Is the Pokémon Showdown format name known and imported?
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isImported(DateTime $month, string $showdownFormatName) : bool
	{
		return isset($this->formatsToImport[$month->format('Y-m-d')][$showdownFormatName]);
	}

	/**
	 * Is the Pokémon Showdown format name known and ignored?
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isIgnored(DateTime $month, string $showdownFormatName) : bool
	{
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return
			isset($this->formatsToIgnore[$month->format('Y-m-d')])
			&& array_key_exists($showdownFormatName, $this->formatsToIgnore[$month->format('Y-m-d')])
		;
	}

	/**
	 * Is the Pokémon Showdown format name known?
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return bool
	 */
	public function isKnown(DateTime $month, string $showdownFormatName) : bool
	{
		return $this->isImported($month, $showdownFormatName)
			|| $this->isIgnored($month, $showdownFormatName)
		;
	}

	/**
	 * Add a Pokémon Showdown format name to the list of unknown formats.
	 *
	 * @param DateTime $month
	 * @param string $showdownFormatName
	 *
	 * @return void
	 */
	public function addUnknown(DateTime $month, string $showdownFormatName) : void
	{
		$this->unknownFormats[$month->format('Y-m-d')][$showdownFormatName] = $showdownFormatName;
	}

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
	public function getFormatId(DateTime $month, string $showdownFormatName) : FormatId
	{
		// If the format is imported, return the format id.
		if ($this->isImported($month, $showdownFormatName)) {
			return $this->formatsToImport[$month->format('Y-m-d')][$showdownFormatName];
		}

		// If the format is not known, add it to the list of unknown formats.
		if (!$this->isKnown($month, $showdownFormatName)) {
			$this->addUnknown($month, $showdownFormatName);
		}

		throw new FormatNotImportedException(
			'Format should not be imported: ' . $showdownFormatName
		);
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
