<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Versions\Generation;

class Move
{
	/** @var MoveId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var Generation $introducedInGeneration */
	private $introducedInGeneration;

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
	 * @return Generation
	 */
	public function getIntroducedInGeneration() : Generation
	{
		return $this->introducedInGeneration;
	}
}
