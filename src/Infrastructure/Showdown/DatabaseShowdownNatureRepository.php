<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Import\Showdown\NatureNotImportedException;
use Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureId;
use PDO;

final class DatabaseShowdownNatureRepository implements ShowdownNatureRepositoryInterface
{
	/** @var NatureId[] $naturesToImport */
	private array $naturesToImport = [];

	/** @var array<string, int> $naturesToIgnore */
	private array $naturesToIgnore = [];

	/** @var string[] $unknownNatures */
	private array $unknownNatures = [];


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
				1
			FROM `showdown_natures_to_ignore`'
		);
		$stmt->execute();
		$this->naturesToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown nature name known and imported?
	 */
	public function isImported(string $showdownNatureName) : bool
	{
		return isset($this->naturesToImport[$showdownNatureName]);
	}

	/**
	 * Is the Pokémon Showdown nature name known and ignored?
	 */
	public function isIgnored(string $showdownNatureName) : bool
	{
		return isset($this->naturesToIgnore[$showdownNatureName]);
	}

	/**
	 * Is the Pokémon Showdown nature name known?
	 */
	public function isKnown(string $showdownNatureName) : bool
	{
		return $this->isImported($showdownNatureName)
			|| $this->isIgnored($showdownNatureName)
		;
	}

	/**
	 * Add a Pokémon Showdown nature name to the list of unknown natures.
	 */
	public function addUnknown(string $showdownNatureName) : void
	{
		$this->unknownNatures[$showdownNatureName] = $showdownNatureName;
	}

	/**
	 * Get the nature id of a Pokémon Showdown nature name.
	 *
	 * @throws NatureNotImportedException if $showdownNatureName is not an
	 *     imported nature name.
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
