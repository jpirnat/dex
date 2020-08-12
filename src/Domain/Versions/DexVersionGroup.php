<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class DexVersionGroup
{
	private VersionGroupId $id;
	private string $identifier;
	private GenerationId $generationId;
	private string $icon;
	private string $name;

	/**
	 * Constructor.
	 *
	 * @param VersionGroupId $versionGroupId
	 * @param string $identifier
	 * @param GenerationId $generationId
	 * @param string $icon
	 * @param string $name
	 */
	public function __construct(
		VersionGroupId $versionGroupId,
		string $identifier,
		GenerationId $generationId,
		string $icon,
		string $name
	) {
		$this->id = $versionGroupId;
		$this->identifier = $identifier;
		$this->generationId = $generationId;
		$this->icon = $icon;
		$this->name = $name;
	}

	/**
	 * Get the dex version group's id.
	 *
	 * @return VersionGroupId
	 */
	public function getId() : VersionGroupId
	{
		return $this->id;
	}

	/**
	 * Get the dex version group's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the dex version group's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the dex version group's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the dex version group's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
