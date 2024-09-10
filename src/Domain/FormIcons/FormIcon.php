<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class FormIcon
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private FormId $formId,
		private bool $isFemale,
		private bool $isRight,
		private bool $isShiny,
		private string $image,
	) {}

	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	public function getFormId() : FormId
	{
		return $this->formId;
	}

	public function isFemale() : bool
	{
		return $this->isFemale;
	}

	public function isRight() : bool
	{
		return $this->isRight;
	}

	public function isShiny() : bool
	{
		return $this->isShiny;
	}

	public function getImage() : string
	{
		return $this->image;
	}
}
