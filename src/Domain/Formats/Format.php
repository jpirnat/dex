<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Versions\GenerationId;

final class Format
{
	private FormatId $id;
	private string $identifier;
	private string $name;
	private GenerationId $generationId;
	private int $level;
	private int $fieldSize;
	private string $smogonDexIdentifier;

	/**
	 * Constructor.
	 *
	 * @param FormatId $formatId
	 * @param string $identifier
	 * @param string $name
	 * @param GenerationId $generationId
	 * @param int $level
	 * @param int $fieldSize
	 * @param string $smogonDexIdentifier
	 */
	public function __construct(
		FormatId $formatId,
		string $identifier,
		string $name,
		GenerationId $generationId,
		int $level,
		int $fieldSize,
		string $smogonDexIdentifier
	) {
		$this->id = $formatId;
		$this->identifier = $identifier;
		$this->name = $name;
		$this->generationId = $generationId;
		$this->level = $level;
		$this->fieldSize = $fieldSize;
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
	 * Get the format's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
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
	 * Get the format's Smogon dex identifier.
	 *
	 * @return string
	 */
	public function getSmogonDexIdentifier() : string
	{
		return $this->smogonDexIdentifier;
	}
}
