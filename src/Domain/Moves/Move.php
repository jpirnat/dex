<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Versions\Generation;

class Move
{
	/** @var MoveId $id */
	protected $id;

	/** @var string $identifier */
	protected $identifier;

	/** @var Generation $introducedInGeneration */
	protected $introducedInGeneration;

	/**
	 * Constructor.
	 *
	 * @param MoveId $moveId
	 * @param string $identifier
	 * @param Generation $introducedInGeneration
	 */
	public function __construct(
		MoveId $moveId,
		string $identifier,
		Generation $introducedInGeneration
	) {
		$this->id = $moveId;
		$this->identifier = $identifier;
		$this->introducedInGeneration = $introducedInGeneration;
	}

	/**
	 * Get the move's id.
	 *
	 * @return MoveId
	 */
	public function id() : MoveId
	{
		return $this->id;
	}

	/**
	 * Get the move's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation this move was introduced in.
	 *
	 * @return Generation
	 */
	public function introducedInGeneration() : Generation
	{
		return $this->introducedInGeneration;
	}
}
