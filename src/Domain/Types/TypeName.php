<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Languages\LanguageId;

final class TypeName
{
	/** @var LanguageId $languageId */
	private $languageId;

	/** @var TypeId $typeId */
	private $typeId;

	/** @var string $name */
	private $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param TypeId $typeId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		TypeId $typeId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->typeId = $typeId;
		$this->name = $name;
	}

	/**
	 * Get the type name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the type name's type id.
	 *
	 * @return TypeId
	 */
	public function getTypeId() : TypeId
	{
		return $this->typeId;
	}

	/**
	 * Get the type name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
