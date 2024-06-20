<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Moves\ZPowerEffects\ZPowerEffectId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface VgMoveRepositoryInterface
{
	/**
	 * Get the Z-Move image.
	 */
	public function getZMoveImage(MoveId $moveId, LanguageId $languageId) : string;

	/**
	 * Get a version group move by its version group and move.
	 *
	 * @throws VgMoveNotFoundException if no version group move exists with this
	 *     version group and move.
	 */
	public function getByVgAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : VgMove;

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
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array;
}
