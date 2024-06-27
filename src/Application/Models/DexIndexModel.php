<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Versions\GenerationId;

final class DexIndexModel
{
	private bool $showAbilities = false;
	private bool $showNatures = false;


	public function __construct(
		private VersionGroupModel $versionGroupModel,
	) {}


	/**
	 * Set data for the dex index page.
	 */
	public function setData(
		string $vgIdentifier,
	) : void {
		$this->showAbilities = false;
		$this->showNatures = false;

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);
		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$this->showAbilities = $versionGroupId->hasAbilities();
		$this->showNatures = $versionGroupId->hasNatures();
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getShowAbilities() : bool
	{
		return $this->showAbilities;
	}

	public function getShowNatures() : bool
	{
		return $this->showNatures;
	}
}
