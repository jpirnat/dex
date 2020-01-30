<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class AbilityDescription
{
	private GenerationId $generationId;
	private LanguageId $languageId;
	private AbilityId $abilityId;
	private string $description;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 * @param string $description
	 */
	public function __construct(
		GenerationId $generationId,
		LanguageId $languageId,
		AbilityId $abilityId,
		string $description
	) {
		$this->generationId = $generationId;
		$this->languageId = $languageId;
		$this->abilityId = $abilityId;
		$this->description = $description;
	}

	/**
	 * Get the ability description's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
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
