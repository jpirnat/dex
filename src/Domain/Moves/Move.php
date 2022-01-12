<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Move
{
	public function __construct(
		private MoveId $id,
		private string $identifier,
		private VersionGroupId $introducedInVersionGroupId,
		private bool $isZMove,
	) {}

	/**
	 * Get the move's id.
	 */
	public function getId() : MoveId
	{
		return $this->id;
	}

	/**
	 * Get the move's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group id this move was introduced in.
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}

	/**
	 * Is this move a Z-Move?
	 */
	public function isZMove() : bool
	{
		return $this->isZMove;
	}
}
