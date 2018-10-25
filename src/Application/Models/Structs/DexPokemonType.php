<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

class DexPokemonType
{
	/** @var string $typeIdentifier */
	private $typeIdentifier;

	/** @var string $typeIcon */
	private $typeIcon;

	/**
	 * Constructor.
	 *
	 * @param string $typeIdentifier
	 * @param string $typeIcon
	 */
	public function __construct(
		string $typeIdentifier,
		string $typeIcon
	) {
		$this->typeIdentifier = $typeIdentifier;
		$this->typeIcon = $typeIcon;
	}

	/**
	 * Get the type identifier.
	 *
	 * @return string
	 */
	public function getTypeIdentifier() : string
	{
		return $this->typeIdentifier;
	}

	/**
	 * Get the type icon.
	 *
	 * @return string
	 */
	public function getTypeIcon() : string
	{
		return $this->typeIcon;
	}
}
