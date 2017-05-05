<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Versions\Generation;

class Format
{
	/** @var FormatId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var Generation $generation */
	private $generation;

	/** @var int $level */
	private $level;

	/** @var int $fieldSize */
	private $fieldSize;

	/** @var int $teamSize */
	private $teamSize;

	/** @var int $inBattleTeamSize */
	private $inBattleTeamSize;

	/**
	 * Constructor.
	 *
	 * @param FormatId $formatId
	 * @param string $identifier
	 * @param Generation $generation
	 * @param int $level
	 * @param int $fieldSize
	 * @param int $teamSize
	 * @param int $inBattleTeamSize
	 */
	public function __construct(
		FormatId $formatId,
		string $identifier,
		Generation $generation,
		int $level,
		int $fieldSize,
		int $teamSize,
		int $inBattleTeamSize
	) {
		$this->id = $formatId;
		$this->identifier = $identifier;
		$this->generation = $generation;
		$this->level = $level;
		$this->fieldSize = $fieldSize;
		$this->teamSize = $teamSize;
		$this->inBattleTeamSize = $inBattleTeamSize;
	}

	/**
	 * Get the format's id.
	 *
	 * @return FormatId
	 */
	public function id() : FormatId
	{
		return $this->id;
	}

	/**
	 * Get the format's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the format's generation.
	 *
	 * @return Generation
	 */
	public function generation() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the format's level.
	 *
	 * @return int
	 */
	public function level() : int
	{
		return $this->level;
	}

	/**
	 * Get the format's field size.
	 *
	 * @return int
	 */
	public function fieldSize() : int
	{
		return $this->fieldSize;
	}

	/**
	 * Get the format's team size.
	 *
	 * @return int
	 */
	public function teamSize() : int
	{
		return $this->teamSize;
	}

	/**
	 * Get the format's in-battle team size.
	 *
	 * @return int
	 */
	public function inBattleTeamSize() : int
	{
		return $this->inBattleTeamSize;
	}
}
