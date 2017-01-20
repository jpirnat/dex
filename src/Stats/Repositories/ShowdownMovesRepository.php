<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownMovesRepository
{
	/** @var int[] $movesToImport */
	protected $movesToImport;

	/** @var ?int[] $movesToIgnore */
	protected $movesToIgnore;

	/** @var string[] $unknownMoves */
	protected $unknownMoves = [];

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
		$this->movesToImport = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`move_id`
			FROM `showdown_moves_to_ignore`'
		);
		$stmt->execute();
		$this->movesToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
		return isset($this->movesToIgnore[$showdownMoveName]);
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
		return isset($this->movesToImport[$showdownMoveName])
			|| isset($this->movesToIgnore[$showdownMoveName])
		;
	}

	/**
	 * Get the move id of a Pokémon Showdown move name.
	 *
	 * @param string $showdownMoveName
	 *
	 * @throws Exception if $showdownMoveName is not an imported name.
	 *
	 * @return int
	 */
	public function getMoveId(string $showdownMoveName) : int
	{
		// If the move is imported, return the move id.
		if ($this->isImported($showdownMoveName)) {
			return $this->movesToImport[$showdownMoveName];
		}

		// If the move is not known, add it to the list of unknown moves.
		if (!$this->isKnown($showdownMoveName)) {
			$this->addUnknown($showdownMoveName);
		}

		throw new Exception('Move should not be imported: ' . $showdownMoveName);
	}
}
