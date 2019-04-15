<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

class DexType
{
	/** @var string $identifier */
	private $identifier;

	/** @var string $icon */
	private $icon;

	/** @var string $name */
	private $name;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $icon
	 * @param string $name
	 */
	public function __construct(
		string $identifier,
		string $icon,
		string $name
	) {
		$this->identifier = $identifier;
		$this->icon = $icon;
		$this->name = $name;
	}

	/**
	 * Get the type's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the type's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the type's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
