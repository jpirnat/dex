<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Models;

use Jp\Dex\Domain\Forms\FormId;

final readonly class Model
{
	public function __construct(
		private FormId $formId,
		private bool $isShiny,
		private bool $isBack,
		private bool $isFemale,
		private int $attackingIndex,
		private string $image,
	) {}

	public function getFormId() : FormId
	{
		return $this->formId;
	}

	public function isShiny() : bool
	{
		return $this->isShiny;
	}

	public function isBack() : bool
	{
		return $this->isBack;
	}

	public function isFemale() : bool
	{
		return $this->isFemale;
	}

	public function getAttackingIndex() : int
	{
		return $this->attackingIndex;
	}

	public function getImage() : string
	{
		return $this->image;
	}
}
