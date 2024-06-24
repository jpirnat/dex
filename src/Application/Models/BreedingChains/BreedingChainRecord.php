<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\BreedingChains;

use Jp\Dex\Domain\Versions\DexVersionGroup;

final readonly class BreedingChainRecord
{
	private string $formIcon;
	private string $pokemonIdentifier;
	private string $pokemonName;
	private DexVersionGroup $versionGroup;

	/** @var string[] $eggGroupNames */
	private array $eggGroupNames;

	private int $baseEggCycles;
	private string $genderRatioIcon;
	private string $genderRatioText;
	private string $moveMethod;


	/**
	 * Constructor.
	 *
	 * @param string[] $eggGroupNames
	 */
	public function __construct(
		string $formIcon,
		string $pokemonIdentifier,
		string $pokemonName,
		DexVersionGroup $versionGroup,
		array $eggGroupNames,
		int $baseEggCycles,
		string $genderRatioIcon,
		string $genderRatioText,
		string $moveMethod,
	) {
		$this->formIcon = $formIcon;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->pokemonName = $pokemonName;
		$this->versionGroup = $versionGroup;
		$this->eggGroupNames = $eggGroupNames;
		$this->baseEggCycles = $baseEggCycles;
		$this->genderRatioIcon = $genderRatioIcon;
		$this->genderRatioText = $genderRatioText;
		$this->moveMethod = $moveMethod;
	}


	/**
	 * Get the form icon.
	 */
	public function getFormIcon() : string
	{
		return $this->formIcon;
	}

	/**
	 * Get the Pokémon identifier.
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the Pokémon name.
	 */
	public function getPokemonName() : string
	{
		return $this->pokemonName;
	}

	/**
	 * Get the version group.
	 */
	public function getVersionGroup() : DexVersionGroup
	{
		return $this->versionGroup;
	}

	/**
	 * Get the egg group names.
	 *
	 * @return string[]
	 */
	public function getEggGroupNames() : array
	{
		return $this->eggGroupNames;
	}

	/**
	 * Get the base egg cycles.
	 */
	public function getBaseEggCycles() : int
	{
		return $this->baseEggCycles;
	}

	/**
	 * Get the gender ratio icon.
	 */
	public function getGenderRatioIcon() : string
	{
		return $this->genderRatioIcon;
	}

	/**
	 * Get the gender ratio text.
	 */
	public function getGenderRatioText() : string
	{
		return $this->genderRatioText;
	}

	/**
	 * Get the move method.
	 */
	public function getMoveMethod() : string
	{
		return $this->moveMethod;
	}
}
