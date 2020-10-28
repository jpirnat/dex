<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class Generation
{
	public function __construct(
		private GenerationId $id,
		private string $identifier,
		private string $icon,
	) {}

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
