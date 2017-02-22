<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownNatureRepository
{
	/** @var int[] $naturesToImport */
	protected $naturesToImport = [];

	/** @var ?int[] $naturesToIgnore */
	protected $naturesToIgnore = [];

	/** @var string[] $unknownNatures */
	protected $unknownNatures = [];

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
				`nature_id`
			FROM `showdown_natures_to_import`'
		);
		$stmt->execute();
		$this->naturesToImport = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`nature_id`
			FROM `showdown_natures_to_ignore`'
		);
		$stmt->execute();
		$this->naturesToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown nature name known and imported?
	 *
	 * @param string $showdownNatureName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownNatureName) : bool
	{
		return isset($this->naturesToImport[$showdownNatureName]);
	}

	/**
	 * Is the Pokémon Showdown nature name known and ignored?
	 *
	 * @param string $showdownNatureName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownNatureName) : bool
	{
		return isset($this->naturesToIgnore[$showdownNatureName]);
	}

	/**
	 * Is the Pokémon Showdown nature name known?
	 *
	 * @param string $showdownNatureName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownNatureName) : bool
	{
		return isset($this->naturesToImport[$showdownNatureName])
			|| isset($this->naturesToIgnore[$showdownNatureName])
		;
	}

	/**
	 * Add a Pokémon Showdown nature name to the list of unknown natures.
	 *
	 * @param string $showdownNatureName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownNatureName) : void
	{
		$this->unknownNatures[$showdownNatureName] = $showdownNatureName;
	}

	/**
	 * Get the nature id of a Pokémon Showdown nature name.
	 *
	 * @param string $showdownNatureName
	 *
	 * @throws Exception if $showdownNatureName is not an imported name.
	 *
	 * @return int
	 */
	public function getNatureId(string $showdownNatureName) : int
	{
		// If the nature is imported, return the nature id.
		if ($this->isImported($showdownNatureName)) {
			return $this->naturesToImport[$showdownNatureName];
		}

		// If the nature is not known, add it to the list of unknown natures.
		if (!$this->isKnown($showdownNatureName)) {
			$this->addUnknown($showdownNatureName);
		}

		throw new Exception('Nature should not be imported: ' . $showdownNatureName);
	}

	/**
	 * Get the names of the unknown natures the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownNatures;
	}
}
