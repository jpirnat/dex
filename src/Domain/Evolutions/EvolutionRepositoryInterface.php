<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface EvolutionRepositoryInterface
{
	/**
	 * Get evolutions that evolve from this form.
	 *
	 * @return Evolution[] Ordered by evo into id.
	 */
	public function getByEvoFrom(VersionGroupId $versionGroupId, FormId $evoFromId) : array;

	/**
	 * Get evolutions that evolve into this form.
	 *
	 * @return Evolution[] Ordered by evo from id.
	 */
	public function getByEvoInto(VersionGroupId $versionGroupId, FormId $evoIntoId) : array;

	/**
	 * Get evolutions triggered by this item in some way.
	 *
	 * @return Evolution[]
	 */
	public function getByItem(VersionGroupId $versionGroupId, ItemId $itemId) : array;

	/**
	 * Get all evolutions.
	 *
	 * @return Evolution[]
	 */
	public function getAll() : array;
}
