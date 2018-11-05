<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TypeIcons;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

class TypeIcon
{
	/** @var GenerationId $generationId */
	private $generationId;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var TypeId $typeId */
	private $typeId;

	/** @var string $image */
	private $image;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 * @param string $image
	 */
	public function __construct(
		GenerationId $generationId,
		LanguageId $languageId,
		TypeId $typeId,
		string $image
	) {
		$this->generationId = $generationId;
		$this->languageId = $languageId;
		$this->typeId = $typeId;
		$this->image = $image;
	}

	/**
	 * Get the type icon's image.
	 *
	 * @return string
	 */
	public function getImage() : string
	{
		return $this->image;
	}
}
