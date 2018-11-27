<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Versions\GenerationId;

class Format
{
	/** @var FormatId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var GenerationId $generationId */
	private $generationId;

	/** @var int $level */
	private $level;

	/** @var int $fieldSize */
	private $fieldSize;

	/** @var int $teamSize */
	private $teamSize;

	/** @var int $inBattleTeamSize */
	private $inBattleTeamSize;

	/** @var string $smogonDexIdentifier */
	private $smogonDexIdentifier;

	/**
	 * Constructor.
	 *
	 * @param FormatId $formatId
	 * @param string $identifier
	 * @param GenerationId $generationId
	 * @param int $level
	 * @param int $fieldSize
	 * @param int $teamSize
	 * @param int $inBattleTeamSize
	 * @param string $smogonDexIdentifier
	 */
	public function __construct(
		FormatId $formatId,
		string $identifier,
		GenerationId $generationId,
		int $level,
		int $fieldSize,
		int $teamSize,
		int $inBattleTeamSize,
		string $smogonDexIdentifier
	) {
		$this->id = $formatId;
		$this->identifier = $identifier;
		$this->generationId = $generationId;
		$this->level = $level;
		$this->fieldSize = $fieldSize;
		$this->teamSize = $teamSize;
		$this->inBattleTeamSize = $inBattleTeamSize;
		$this->smogonDexIdentifier = $smogonDexIdentifier;
	}

	/**
	 * Get the format's id.
	 *
	 * @return FormatId
	 */
	public function getId() : FormatId
	{
		return $this->id;
	}

	/**
	 * Get the format's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the format's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the format's level.
	 *
	 * @return int
	 */
	public function getLevel() : int
	{
		return $this->level;
	}

	/**
	 * Get the format's field size.
	 *
	 * @return int
	 */
	public function getFieldSize() : int
	{
		return $this->fieldSize;
	}

	/**
	 * Get the format's team size.
	 *
	 * @return int
	 */
	public function getTeamSize() : int
	{
		return $this->teamSize;
	}

	/**
	 * Get the format's in-battle team size.
	 *
	 * @return int
	 */
	public function getInBattleTeamSize() : int
	{
		return $this->inBattleTeamSize;
	}

	/**
	 * Get the format's Smogon dex identifier.
	 *
	 * @return string
	 */
	public function getSmogonDexIdentifier() : string
	{
		return $this->smogonDexIdentifier;
	}
}
