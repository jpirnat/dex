<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Versions\VersionGroupId;

class Move
{
	/** @var MoveId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var VersionGroupId $introducedInVersionGroupId */
	private $introducedInVersionGroupId;

	/**
	 * Constructor.
	 *
	 * @param MoveId $moveId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 */
	public function __construct(
		MoveId $moveId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId
	) {
		$this->id = $moveId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
	}

	/**
	 * Get the move's id.
	 *
	 * @return MoveId
	 */
	public function getId() : MoveId
	{
		return $this->id;
	}

	/**
	 * Get the move's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation this move was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInGeneration() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}
}
