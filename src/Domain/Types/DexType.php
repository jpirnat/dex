<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

final class DexType
{
	/** @var TypeId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var string $icon */
	private $icon;

	/** @var string $name */
	private $name;

	/**
	 * Constructor.
	 *
	 * @param TypeId $typeId
	 * @param string $identifier
	 * @param string $icon
	 * @param string $name
	 */
	public function __construct(
		TypeId $typeId,
		string $identifier,
		string $icon,
		string $name
	) {
		$this->id = $typeId;
		$this->identifier = $identifier;
		$this->icon = $icon;
		$this->name = $name;
	}

	/**
	 * Get the type's id.
	 *
	 * @return TypeId
	 */
	public function getId() : TypeId
	{
		return $this->id;
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
