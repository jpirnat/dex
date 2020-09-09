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
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 *
	 * @throws GenerationMoveNotFoundException if no generation move exists with
	 *     this generation and move.
	 *
	 * @return GenerationMove
	 */
	public function getByGenerationAndMove(
		GenerationId $generationId,
		MoveId $moveId
	) : GenerationMove;

	/**
	 * Get the infliction.
	 *
	 * @param InflictionId $inflictionId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getInfliction(InflictionId $inflictionId, LanguageId $languageId) : array;

	/**
	 * Get the target.
	 *
	 * @param TargetId $targetId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getTarget(TargetId $targetId, LanguageId $languageId) : array;

	/**
	 * Get the Z-Move.
	 *
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getZMove(MoveId $moveId, LanguageId $languageId) : array;

	/**
	 * Get the Max Move.
	 *
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getMaxMove(MoveId $moveId, LanguageId $languageId) : array;

	/**
	 * Get the Z-Power Effect.
	 *
	 * @param ZPowerEffectId $zPowerEffectId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getZPowerEffect(ZPowerEffectId $zPowerEffectId, LanguageId $languageId) : array;

	/**
	 * Get the move's stat changes.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getStatChanges(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array;
}
