<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedPokemonSearch;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Abilities\AbilityNotFoundException;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;

final class AdvancedPokemonSearchSubmitModel
{
	/** @var DexPokemon[] $pokemons */
	private array $pokemons = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly AbilityRepositoryInterface $abilityRepository,
		private readonly AdvancedPokemonSearchQueriesInterface $queries,
	) {}


	/**
	 * Set data for the advanced Pokémon search page.
	 *
	 * @param string[] $moveIdentifiers
	 */
	public function setData(
		string $vgIdentifier,
		string $abilityIdentifier,
		array $moveIdentifiers,
		string $includeTransferMoves,
		LanguageId $languageId,
	) : void {
		try {
			$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);
		} catch (VersionGroupNotFoundException) {
			return;
		}

		$moveIdentifiersToIds = $this->queries->getMoveIdentifiersToIds();

		$abilityId = null;
		if ($abilityIdentifier) {
			try {
				$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);
				$abilityId = $ability->getId();
			} catch (AbilityNotFoundException) {
			}
		}

		$moveIds = [];
		foreach ($moveIdentifiers as $moveIdentifier) {
			if (isset($moveIdentifiersToIds[$moveIdentifier])) {
				$moveIds[] = $moveIdentifiersToIds[$moveIdentifier];
			}
		}

		$includeTransferMoves = (bool) $includeTransferMoves;

		$this->pokemons = $this->queries->search(
			$versionGroupId,
			$abilityId,
			$moveIds,
			$includeTransferMoves,
			$languageId,
		);
	}

	/**
	 * @return DexPokemon[]
	 */
	public function getPokemons() : array
	{
		return $this->pokemons;
	}
}
