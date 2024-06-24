<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Versions\GenerationId;

final readonly class MoveMethod
{
	public function __construct(
		private MoveMethodId $id,
		private string $identifier,
		private GenerationId $introducedInGenerationId,
		private int $sort,
	) {}

	/**
	 * Get the move method's id.
	 */
	public function getId() : MoveMethodId
	{
		return $this->id;
	}

	/**
	 * Get the move method's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation id this move method was introduced in.
	 */
	public function getIntroducedInGenerationId() : GenerationId
	{
		return $this->introducedInGenerationId;
	}

	/**
	 * Get the move method's sort value.
	 */
	public function getSort() : int
	{
		return $this->sort;
	}
}
