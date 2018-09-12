<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;

class AbilityDescription
{
	/** @var Generation $generation */
	private $generation;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var AbilityId $abilityId */
	private $abilityId;

	/** @var string $description */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 * @param string $description
	 */
	public function __construct(
		Generation $generation,
		LanguageId $languageId,
		AbilityId $abilityId,
		string $description
	) {
		$this->generation = $generation;
		$this->languageId = $languageId;
		$this->abilityId = $abilityId;
		$this->description = $description;
	}

	/**
	 * Get the ability description's generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the ability description's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the ability description's ability id.
	 *
	 * @return AbilityId
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Get the ability description's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
