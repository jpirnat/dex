<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

final readonly class Move
{
	public function __construct(
		private MoveId $id,
		private string $identifier,
		private MoveType $type,
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
	 * Get the move's type.
	 */
	public function getType() : MoveType
	{
		return $this->type;
	}
}
