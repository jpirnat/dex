<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Import\Showdown\MoveNotImportedException;
use Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use PDO;

final class DatabaseShowdownMoveRepository implements ShowdownMoveRepositoryInterface
{
	/** @var MoveId[] $movesToImport */
	private $movesToImport = [];

	/** @var ?MoveId[] $movesToIgnore */
	private $movesToIgnore = [];

	/** @var string[] $unknownMoves */
	private $unknownMoves = [];

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
				`move_id`
			FROM `showdown_moves_to_import`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->movesToImport[$result['name']] = new MoveId($result['move_id']);
		}

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`move_id`
			FROM `showdown_moves_to_ignore`'
		);
		$stmt->execute();
		$this->movesToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($result['move_id'] !== null) {
				// The Pokémon Showdown move name has a move id.
				$moveId = new MoveId($result['move_id']);
			} else {
				$moveId = null;
			}

			$this->movesToIgnore[$result['name']] = $moveId;
		}
	}

	/**
	 * Is the Pokémon Showdown move name known and imported?
	 *
	 * @param string $showdownMoveName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownMoveName) : bool
	{
		return isset($this->movesToImport[$showdownMoveName]);
	}

	/**
	 * Is the Pokémon Showdown move name known and ignored?
	 *
	 * @param string $showdownMoveName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownMoveName) : bool
	{
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return array_key_exists($showdownMoveName, $this->movesToIgnore);
	}

	/**
	 * Is the Pokémon Showdown move name known?
	 *
	 * @param string $showdownMoveName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownMoveName) : bool
	{
		return $this->isImported($showdownMoveName)
			|| $this->isIgnored($showdownMoveName)
		;
	}

	/**
	 * Add a Pokémon Showdown move name to the list of unknown moves.
	 *
	 * @param string $showdownMoveName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownMoveName) : void
	{
		$this->unknownMoves[$showdownMoveName] = $showdownMoveName;
	}

	/**
	 * Get the move id of a Pokémon Showdown move name.
	 *
	 * @param string $showdownMoveName
	 *
	 * @throws MoveNotImportedException if $showdownMoveName is not an imported
	 *     move name.
	 *
	 * @return MoveId
	 */
	public function getMoveId(string $showdownMoveName) : MoveId
	{
		// If the move is imported, return the move id.
		if ($this->isImported($showdownMoveName)) {
			return $this->movesToImport[$showdownMoveName];
		}

		// If the move is not known, add it to the list of unknown moves.
		if (!$this->isKnown($showdownMoveName)) {
			$this->addUnknown($showdownMoveName);
		}

		throw new MoveNotImportedException(
			'Move should not be imported: ' . $showdownMoveName
		);
	}

	/**
	 * Get the names of the unknown moves the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownMoves;
	}
}
