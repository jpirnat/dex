<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Stats\Showdown\NatureNotImportedException;
use Jp\Dex\Domain\Stats\Showdown\ShowdownNatureRepositoryInterface;
use PDO;

class DatabaseShowdownNatureRepository implements ShowdownNatureRepositoryInterface
{
	/** @var NatureId[] $naturesToImport */
	private $naturesToImport = [];

	/** @var ?NatureId[] $naturesToIgnore */
	private $naturesToIgnore = [];

	/** @var string[] $unknownNatures */
	private $unknownNatures = [];

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
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->naturesToImport[$result['name']] = new NatureId($result['nature_id']);
		}

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`nature_id`
			FROM `showdown_natures_to_ignore`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($result['nature_id'] !== null) {
				// The Pokémon Showdown nature name has a nature id.
				$natureId = new NatureId($result['nature_id']);
			} else {
				$natureId = null;
			}

			$this->naturesToIgnore[$result['name']] = $natureId;
		}
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
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return array_key_exists($showdownNatureName, $this->naturesToIgnore);
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
		return $this->isImported($showdownNatureName)
			|| $this->isIgnored($showdownNatureName)
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
	 * @throws NatureNotImportedException if $showdownNatureName is not an
	 *     imported nature name.
	 *
	 * @return NatureId
	 */
	public function getNatureId(string $showdownNatureName) : NatureId
	{
		// If the nature is imported, return the nature id.
		if ($this->isImported($showdownNatureName)) {
			return $this->naturesToImport[$showdownNatureName];
		}

		// If the nature is not known, add it to the list of unknown natures.
		if (!$this->isKnown($showdownNatureName)) {
			$this->addUnknown($showdownNatureName);
		}

		throw new NatureNotImportedException(
			'Nature should not be imported: ' . $showdownNatureName
		);
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
