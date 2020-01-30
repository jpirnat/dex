<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Characteristics;

use Jp\Dex\Domain\Languages\LanguageId;

final class CharacteristicName
{
	private LanguageId $languageId;
	private CharacteristicId $characteristicId;
	private string $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param CharacteristicId $characteristicId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		CharacteristicId $characteristicId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->characteristicId = $characteristicId;
		$this->name = $name;
	}

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
