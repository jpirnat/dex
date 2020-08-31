<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Flags;

final class DexFlag
{
	private string $identifier;
	private string $name;
	private string $description;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param string $description
	 */
	public function __construct(
		string $identifier,
		string $name,
		string $description
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->description = $description;
	}

	/**
	 * Get the flag's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the flag's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the flag's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
