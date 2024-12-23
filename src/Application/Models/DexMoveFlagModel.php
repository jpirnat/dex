<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Flags\MoveFlagRepositoryInterface;

final class DexMoveFlagModel
{
	private(set) array $flag = [];

	/** @var DexMove[] $moves */
	private(set) array $moves = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly MoveFlagRepositoryInterface $flagRepository,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
	) {}


	/**
	 * Set data for the dex move flag page.
	 */
	public function setData(
		string $vgIdentifier,
		string $moveFlagIdentifier,
		LanguageId $languageId,
	) : void {
		$this->flag = [];
		$this->moves = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$flag = $this->flagRepository->getByIdentifier($moveFlagIdentifier);

		$this->versionGroupModel->setWithMoveFlag($flag->id);

		$dexFlag = $this->flagRepository->getByIdPlural(
			$versionGroupId,
			$flag->id,
			$languageId,
		);
		$this->flag = [
			'identifier' => $dexFlag->identifier,
			'name' => $dexFlag->name,
			'description' => $dexFlag->description,
		];

		$this->moves = $this->dexMoveRepository->getByVgAndFlag(
			$versionGroupId,
			$flag->id,
			$languageId,
		);
	}
}
