<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Move
{
	/** @var MoveId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var VersionGroupId $introducedInVersionGroupId */
	private $introducedInVersionGroupId;

	/** @var bool $isZMove */
	private $isZMove;

	/**
	 * Constructor.
	 *
	 * @param MoveId $moveId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 * @param bool $isZMove
	 */
	public function __construct(
		MoveId $moveId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId,
		bool $isZMove
	) {
		$this->id = $moveId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
		$this->isZMove = $isZMove;
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
	 * Get the version group id this move was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}

	/**
	 * Is this move a Z-Move?
	 *
	 * @return bool
	 */
	public function isZMove() : bool
	{
		return $this->isZMove;
	}
}
