<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

use Jp\Dex\Domain\Languages\LanguageId;

final class EggGroupName
{
	public function __construct(
		private LanguageId $languageId,
		private EggGroupId $eggGroupId,
		private string $name,
	) {}

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
