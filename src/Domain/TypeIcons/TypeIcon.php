<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TypeIcons;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\Generation;

class TypeIcon
{
	/** @var Generation $generation */
	private $generation;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var TypeId $typeId */
	private $typeId;

	/** @var string $image */
	private $image;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 * @param string $image
	 */
	public function __construct(
		Generation $generation,
		LanguageId $languageId,
		TypeId $typeId,
		string $image
	) {
		$this->generation = $generation;
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
