<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveFlagRepositoryInterface;

final class DexMoveFlagModel
{
	private array $flag = [];

	/** @var DexMove[] $moves */
	private array $moves = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private MoveFlagRepositoryInterface $flagRepository,
		private DexMoveRepositoryInterface $dexMoveRepository,
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
		$flagId = $flag->getId();

		$this->versionGroupModel->setWithMoveFlag($flagId);

		$dexFlag = $this->flagRepository->getByIdPlural(
			$versionGroupId,
			$flagId,
			$languageId,
		);
		$this->flag = [
			'name' => $dexFlag->getName(),
			'description' => $dexFlag->getDescription(),
		];

		$this->moves = $this->dexMoveRepository->getByVgAndFlag(
			$versionGroupId,
			$flagId,
			$languageId,
		);
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getFlag() : array
	{
		return $this->flag;
	}

	/**
	 * @return DexMove[]
	 */
	public function getMoves() : array
	{
		return $this->moves;
	}
}
