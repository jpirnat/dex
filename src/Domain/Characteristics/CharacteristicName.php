<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Characteristics;

use Jp\Dex\Domain\Languages\LanguageId;

final class CharacteristicName
{
	public function __construct(
		private LanguageId $languageId,
		private CharacteristicId $characteristicId,
		private string $name,
	) {}

	/**
	 * Get the characteristic name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the characteristic name's characteristic id.
	 *
	 * @return CharacteristicId
	 */
	public function getCharacteristicId() : CharacteristicId
	{
		return $this->characteristicId;
	}

	/**
	 * Get the characteristic name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
