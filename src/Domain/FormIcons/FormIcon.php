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
		private string $image,
	) {}

	/**
	 * Get the form icon's image.
	 */
	public function getImage() : string
	{
		return $this->image;
	}
}
