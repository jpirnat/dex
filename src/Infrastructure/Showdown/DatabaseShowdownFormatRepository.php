<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Import\Showdown\FormatNotImportedException;
use Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface;
use PDO;

final class DatabaseShowdownFormatRepository implements ShowdownFormatRepositoryInterface
{
	/**
	 * Indexed by month, then Showdown format name.
	 * The value is the format id.
	 *
	 * @var FormatId[][] $formatsToImport
	 */
	private array $formatsToImport = [];

	/**
	 * Indexed by month, then Showdown format name.
	 *
	 * @var array<string, array<string, int>>
	 */
	private array $formatsToIgnore = [];

	/**
	 * Indexed by month, then Showdown format name.
	 * The value is the Showdown format name.
	 *
	 * @var string[][]
	 */
	private array $unknownFormats = [];


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
				`name`
			FROM `showdown_formats_to_ignore`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->formatsToIgnore[$result['month']][$result['name']] = 1;
		}
	}

	/**
	 * Is the Pokémon Showdown format name known and imported?
	 */
	public function isImported(DateTime $month, string $showdownFormatName) : bool
	{
		return isset($this->formatsToImport[$month->format('Y-m-d')][$showdownFormatName]);
	}

	/**
	 * Is the Pokémon Showdown format name known and ignored?
	 */
	public function isIgnored(DateTime $month, string $showdownFormatName) : bool
	{
		return isset($this->formatsToIgnore[$month->format('Y-m-d')][$showdownFormatName]);
	}

	/**
	 * Is the Pokémon Showdown format name known?
	 */
	public function isKnown(DateTime $month, string $showdownFormatName) : bool
	{
		return $this->isImported($month, $showdownFormatName)
			|| $this->isIgnored($month, $showdownFormatName)
		;
	}

	/**
	 * Add a Pokémon Showdown format name to the list of unknown formats.
	 */
	public function addUnknown(DateTime $month, string $showdownFormatName) : void
	{
		$this->unknownFormats[$month->format('Y-m-d')][$showdownFormatName] = $showdownFormatName;
	}

	/**
	 * Get the format id of a Pokémon Showdown format name.
	 *
	 * @throws FormatNotImportedException if $showdownFormatName is not an
	 *     imported format name.
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
			"Format should not be imported: $showdownFormatName."
		);
	}

	/**
	 * Get the names of the unknown formats the repository has tracked.
	 *
	 * @return string[][]
	 */
	public function getUnknown() : array
	{
		return $this->unknownFormats;
	}
}
