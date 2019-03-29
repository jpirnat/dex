<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TypeIcons;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeId;

class TypeIcon
{
	/** @var LanguageId $languageId */
	private $languageId;

	/** @var TypeId $typeId */
	private $typeId;

	/** @var string $icon */
	private $icon;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 * @param string $icon
	 */
	public function __construct(
		LanguageId $languageId,
		TypeId $typeId,
		string $icon
	) {
		$this->languageId = $languageId;
		$this->typeId = $typeId;
		$this->icon = $icon;
	}

	/**
	 * Get the type icon's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the type icon's type id.
	 *
	 * @return TypeId
	 */
	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}

	/**
	 * Get the type icon's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}
}
