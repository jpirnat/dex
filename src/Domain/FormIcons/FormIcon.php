<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\FormIcons;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Versions\GenerationId;

final class FormIcon
{
	private GenerationId $generationId;
	private FormId $formId;
	private bool $isFemale;
	private bool $isRight;
	private string $image;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param FormId $formId
	 * @param bool $isFemale
	 * @param bool $isRight
	 * @param string $image
	 */
	public function __construct(
		GenerationId $generationId,
		FormId $formId,
		bool $isFemale,
		bool $isRight,
		string $image
	) {
		$this->generationId = $generationId;
		$this->formId = $formId;
		$this->isFemale = $isFemale;
		$this->isRight = $isRight;
		$this->image = $image;
	}

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
