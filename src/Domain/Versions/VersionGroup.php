<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class VersionGroup
{
	/** @var VersionGroupId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var GenerationId $generationId */
	private $generationId;

	/** @var string $icon */
	private $icon;

	/** @var int $sort */
	private $sort;

	/**
	 * Constructor.
	 *
	 * @param VersionGroupId $versionGroupId
	 * @param string $identifier
	 * @param GenerationId $generationId
	 * @param string $icon
	 * @param int $sort
	 */
	public function __construct(
		VersionGroupId $versionGroupId,
		string $identifier,
		GenerationId $generationId,
		string $icon,
		int $sort
	) {
		$this->id = $versionGroupId;
		$this->identifier = $identifier;
		$this->generationId = $generationId;
		$this->icon = $icon;
		$this->sort = $sort;
	}

	/**
	 * Get the version group's id.
	 *
	 * @return VersionGroupId
	 */
	public function getId() : VersionGroupId
	{
		return $this->id;
	}

	/**
	 * Get the version group's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the version group's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the version group's sort value.
	 *
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
