<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Versions\GenerationId;

final readonly class DexIndexModel
{
	public function __construct(
		private VersionGroupModel $versionGroupModel,
	) {}


	/**
	 * Set data for the dex index page.
	 */
	public function setData(
		string $vgIdentifier,
	) : void {
		$this->versionGroupModel->setByIdentifier($vgIdentifier);
		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}
}
