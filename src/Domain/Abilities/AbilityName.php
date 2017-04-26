<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;

class AbilityName
{
	/** @var LanguageId $languageId */
	protected $languageId;

	/** @var AbilityId $abilityId */
	protected $abilityId;

	/** @var string $name */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		AbilityId $abilityId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->abilityId = $abilityId;
		$this->name = $name;
	}

	/**
	 * Get the ability name's language id.
	 *
	 * @return LanguageId
	 */
	public function languageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the ability name's ability id.
	 *
	 * @return AbilityId
	 */
	public function abilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Get the ability name's name value.
	 *
	 * @return string
	 */
	public function name() : string
	{
		return $this->name;
	}
}
