<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownAbilityRepository
{
	/** @var int[] $abilitiesToImport */
	protected $abilitiesToImport;

	/** @var ?int[] $abilitiesToIgnore */
	protected $abilitiesToIgnore;

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
		return isset($this->abilitiesToIgnore[$showdownAbilityName]);
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
		return isset($this->abilitiesToImport[$showdownAbilityName])
			|| isset($this->abilitiesToIgnore[$showdownAbilityName])
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
	 * @return int
	 */
	public function getAbilityId(string $showdownAbilityName) : int
	{
		// If the ability is imported, return the ability id.
		if ($this->isImported($showdownAbilityName)) {
			return $this->abilitiesToImport[$showdownAbilityName];
		}

		// If the ability is not known, add it to the list of unknown abilities.
		if (!$this->isKnown($showdownAbilityName)) {
			$this->addUnknown($showdownAbilityName);
		}

		throw new Exception('Ability should not be imported: ' . $showdownAbilityName);
	}
}
