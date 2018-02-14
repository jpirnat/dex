<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Models;

use Jp\Dex\Domain\Forms\FormId;

class Model
{
	/** @var FormId $formId */
	private $formId;

	/** @var bool $isShiny */
	private $isShiny;

	/** @var bool $isBack */
	private $isBack;

	/** @var bool $isFemale */
	private $isFemale;

	/** @var int $attackingIndex */
	private $attackingIndex;

	/** @var string $image */
	private $image;

	/**
	 * Constructor.
	 *
	 * @param FormId $formId
	 * @param bool $isShiny
	 * @param bool $isBack
	 * @param bool $isFemale
	 * @param int $attackingIndex
	 * @param string $image
	 */
	public function __construct(
		FormId $formId,
		bool $isShiny,
		bool $isBack,
		bool $isFemale,
		int $attackingIndex,
		string $image
	) {
		$this->formId = $formId;
		$this->isShiny = $isShiny;
		$this->isBack = $isBack;
		$this->isFemale = $isFemale;
		$this->attackingIndex = $attackingIndex;
		$this->image = $image;
	}

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
