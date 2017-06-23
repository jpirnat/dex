<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

class Nature
{
	/** @var NatureId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/**
	 * Constructor.
	 *
	 * @param NatureId $natureId
	 * @param string $identifier
	 */
	public function __construct(
		NatureId $natureId,
		string $identifier
	) {
		$this->id = $natureId;
		$this->identifier = $identifier;
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
}
