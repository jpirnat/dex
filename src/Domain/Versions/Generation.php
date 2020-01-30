<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class Generation
{
	private GenerationId $id;
	private string $identifier;
	private string $icon;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param string $identifier
	 * @param string $icon
	 */
	public function __construct(
		GenerationId $generationId,
		string $identifier,
		string $icon
	) {
		$this->id = $generationId;
		$this->identifier = $identifier;
		$this->icon = $icon;
	}

	/**
	 * Get the generation's id.
	 *
	 * @return GenerationId
	 */
	public function getId() : GenerationId
	{
		return $this->id;
	}

	/**
	 * Get the generation's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}
}
