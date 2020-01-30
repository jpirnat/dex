<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Languages\LanguageId;

final class EggGroupName
{
	private LanguageId $languageId;
	private EggGroupId $eggGroupId;
	private string $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param EggGroupId $eggGroupId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		EggGroupId $eggGroupId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->eggGroupId = $eggGroupId;
		$this->name = $name;
	}

	/**
	 * Get the egg group name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the egg group name's eggGroup id.
	 *
	 * @return EggGroupId
	 */
	public function getEggGroupId() : EggGroupId
	{
		return $this->eggGroupId;
	}

	/**
	 * Get the egg group name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
