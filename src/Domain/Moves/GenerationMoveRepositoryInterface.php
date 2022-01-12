<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Moves\ZPowerEffects\ZPowerEffectId;
use Jp\Dex\Domain\Versions\GenerationId;

interface GenerationMoveRepositoryInterface
{
	/**
	 * Get a generation move by its generation and move.
	 *
	 * @throws GenerationMoveNotFoundException if no generation move exists with
	 *     this generation and move.
	 */
	public function getByGenerationAndMove(
		GenerationId $generationId,
		MoveId $moveId
	) : GenerationMove;

	/**
	 * Get the infliction.
	 */
	public function getInfliction(InflictionId $inflictionId, LanguageId $languageId) : array;

	/**
	 * Get the target.
	 */
	public function getTarget(TargetId $targetId, LanguageId $languageId) : array;

	/**
	 * Get the Z-Move.
	 */
	public function getZMove(MoveId $moveId, LanguageId $languageId) : array;

	/**
	 * Get the Max Move.
	 */
	public function getMaxMove(MoveId $moveId, LanguageId $languageId) : array;

	/**
	 * Get the Z-Power Effect.
	 */
	public function getZPowerEffect(ZPowerEffectId $zPowerEffectId, LanguageId $languageId) : array;

	/**
	 * Get the move's stat changes.
	 */
	public function getStatChanges(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array;
}
