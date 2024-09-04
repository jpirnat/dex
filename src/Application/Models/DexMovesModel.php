<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveFlagRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexMovesModel
{
	/** @var DexMove[] $moves */
	private array $moves = [];

	private array $flags = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private DexMoveRepositoryInterface $dexMoveRepository,
		private MoveFlagRepositoryInterface $flagRepository,
	) {}


	/**
	 * Set data for the dex moves page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->moves = [];
		$this->flags = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$this->moves = $this->dexMoveRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);

		$flags = $this->flagRepository->getByVersionGroupPlural(
			$versionGroupId,
			$languageId,
		);
		foreach ($flags as $flag) {
			$this->flags[] = [
				'identifier' => $flag->getIdentifier(),
				'name' => $flag->getName(),
				'description' => $flag->getDescription(),
			];
		}
	}


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the moves.
	 *
	 * @return DexMove[]
	 */
	public function getMoves() : array
	{
		return $this->moves;
	}

	public function getFlags() : array
	{
		return $this->flags;
	}
}
