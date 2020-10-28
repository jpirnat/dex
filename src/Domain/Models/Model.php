<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Models;

use Jp\Dex\Domain\Forms\FormId;

final class Model
{
	public function __construct(
		private FormId $formId,
		private bool $isShiny,
		private bool $isBack,
		private bool $isFemale,
		private int $attackingIndex,
		private string $image,
	) {}

	/**
	 * Get the model's image.
	 *
	 * @return string
	 */
	public function getImage() : string
	{
		return $this->image;
	}
}
