<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use Jp\Dex\Domain\Abilities\AbilityId;
use PDO;

class ShowdownAbilityRepository
{
	/** @var int[] $abilitiesToImport */
	protected $abilitiesToImport = [];

	/** @var ?int[] $abilitiesToIgnore */
	protected $abilitiesToIgnore = [];

	/** @var string[] $unknownAbilities */
	protected $unknownAbilities = [];

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
				`ability_id`
			FROM `showdown_abilities_to_import`'
		);
		$stmt->execute();
		$this->abilitiesToImport = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`ability_id`
			FROM `showdown_abilities_to_ignore`'
		);
		$stmt->execute();
		$this->abilitiesToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown ability name known and imported?
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownAbilityName) : bool
	{
		return isset($this->abilitiesToImport[$showdownAbilityName]);
	}

	/**
	 * Is the Pokémon Showdown ability name known and ignored?
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownAbilityName) : bool
	{
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return array_key_exists($showdownAbilityName, $this->abilitiesToIgnore);
	}

	/**
	 * Is the Pokémon Showdown ability name known?
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownAbilityName) : bool
	{
		return $this->isImported($showdownAbilityName)
			|| $this->isIgnored($showdownAbilityName)
		;
	}

	/**
	 * Add a Pokémon Showdown ability name to the list of unknown abilities.
	 *
	 * @param string $showdownAbilityName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownAbilityName) : void
	{
		$this->unknownAbilities[$showdownAbilityName] = $showdownAbilityName;
	}

	/**
	 * Get the ability id of a Pokémon Showdown ability name.
	 *
	 * @param string $showdownAbilityName
	 *
	 * @throws Exception if $showdownAbilityName is not an imported name.
	 *
	 * @return AbilityId
	 */
	public function getAbilityId(string $showdownAbilityName) : AbilityId
	{
		// If the ability is imported, return the ability id.
		if ($this->isImported($showdownAbilityName)) {
			return new AbilityId($this->abilitiesToImport[$showdownAbilityName]);
		}

		// If the ability is not known, add it to the list of unknown abilities.
		if (!$this->isKnown($showdownAbilityName)) {
			$this->addUnknown($showdownAbilityName);
		}

		throw new Exception('Ability should not be imported: ' . $showdownAbilityName);
	}

	/**
	 * Get the names of the unknown abilities the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownAbilities;
	}
}
