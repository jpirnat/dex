<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Versions\GenerationId;

class MoveMethod
{
	/** @var MoveMethodId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var GenerationId $introducedInGenerationId */
	private $introducedInGenerationId;

	/** @var int $sort */
	private $sort;

	/**
	 * Constructor.
	 *
	 * @param MoveMethodId $moveMethodId
	 * @param string $identifier
	 * @param GenerationId $introducedInGenerationId
	 * @param int $sort
	 */
	public function __construct(
		MoveMethodId $moveMethodId,
		string $identifier,
		GenerationId $introducedInGenerationId,
		int $sort
	) {
		$this->id = $moveMethodId;
		$this->identifier = $identifier;
		$this->introducedInGenerationId = $introducedInGenerationId;
		$this->sort = $sort;
	}

	/**
	 * Get the move method's id.
	 *
	 * @return MoveMethodId
	 */
	public function getId() : MoveMethodId
	{
		return $this->id;
	}

	/**
	 * Get the move method's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation id this move method was introduced in.
	 *
	 * @return GenerationId
	 */
	public function getIntroducedInGenerationId() : GenerationId
	{
		return $this->introducedInGenerationId;
	}

	/**
	 * Get the move method's sort value.
	 *
	 * @return int
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
