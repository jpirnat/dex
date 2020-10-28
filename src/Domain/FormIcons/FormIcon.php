<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\GenerationId;

final class FormIcon
{
	public function __construct(
		private GenerationId $generationId,
		private FormId $formId,
		private bool $isFemale,
		private bool $isRight,
		private string $image,
	) {}

	/**
	 * Get the form icon's image.
	 *
	 * @return string
	 */
	public function getImage() : string
	{
		return $this->image;
	}
}
