<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Versions\GenerationId;

final class MoveMethod
{
	public function __construct(
		private MoveMethodId $id,
		private string $identifier,
		private GenerationId $introducedInGenerationId,
		private int $sort,
	) {}

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
