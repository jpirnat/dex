<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Stats\StatValueContainer;

class Nature
{
	/** @var NatureId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var StatValueContainer $statModifiers */
	private $statModifiers;

	/**
	 * Constructor.
	 *
	 * @param NatureId $natureId
	 * @param string $identifier
	 * @param StatValueContainer $statModifiers
	 */
	public function __construct(
		NatureId $natureId,
		string $identifier,
		StatValueContainer $statModifiers
	) {
		$this->id = $natureId;
		$this->identifier = $identifier;
		$this->statModifiers = $statModifiers;
	}

	/**
	 * Get the nature's id.
	 *
	 * @return NatureId
	 */
	public function getId() : NatureId
	{
		return $this->id;
	}

	/**
	 * Get the nature's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the nature's stat modifiers.
	 *
	 * @return StatValueContainer
	 */
	public function getStatModifiers() : StatValueContainer
	{
		return $this->statModifiers;
	}
}
