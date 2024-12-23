<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Flags\MoveFlagRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexMovesModel
{
	/** @var DexMove[] $moves */
	private(set) array $moves = [];

	private(set) array $flags = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
		private readonly MoveFlagRepositoryInterface $flagRepository,
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
				'identifier' => $flag->identifier,
				'name' => $flag->name,
				'description' => $flag->description,
			];
		}
	}
}
