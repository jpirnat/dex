<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Flags;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;

interface FlagRepositoryInterface
{
	/**
	 * Get all dex flags in this generation.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexFlag[] Indexed by flag id.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get this move's flags.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 *
	 * @return FlagId[] Indexed by flag id.
	 */
	public function getByMove(
		GenerationId $generationId,
		MoveId $moveId
	) : array;
}
