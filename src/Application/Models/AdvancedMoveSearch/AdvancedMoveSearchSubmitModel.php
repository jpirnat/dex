<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedMoveSearch;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;

final class AdvancedMoveSearchSubmitModel
{
	/** @var DexMove[] $moves */
	private(set) array $moves = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly AdvancedMoveSearchQueriesInterface $queries,
	) {}


	/**
	 * Set data for the advanced move search page.
	 *
	 * @param string[] $typeIdentifiers
	 * @param string[] $categoryIdentifiers
	 * @param string[] $yesFlagIdentifiers
	 * @param string[] $noFlagIdentifiers
	 */
	public function setData(
		string $vgIdentifier,
		array $typeIdentifiers,
		array $categoryIdentifiers,
		array $yesFlagIdentifiers,
		array $noFlagIdentifiers,
		string $pokemonIdentifier,
		string $includeTransferMoves,
		LanguageId $languageId,
	) : void {
		try {
			$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);
		} catch (VersionGroupNotFoundException) {
			return;
		}

		$typeIdentifiersToIds = $this->queries->getTypeIdentifiersToIds();
		$categoryIdentifiersToIds = $this->queries->getCategoryIdentifiersToIds();
		$flagIdentifiersToIds = $this->queries->getFlagIdentifiersToIds();

		$typeIds = [];
		foreach ($typeIdentifiers as $typeIdentifier) {
			if (isset($typeIdentifiersToIds[$typeIdentifier])) {
				$typeIds[] = $typeIdentifiersToIds[$typeIdentifier];
			}
		}

		$categoryIds = [];
		foreach ($categoryIdentifiers as $categoryIdentifier) {
			if (isset($categoryIdentifiersToIds[$categoryIdentifier])) {
				$categoryIds[] = $categoryIdentifiersToIds[$categoryIdentifier];
			}
		}

		$yesFlagIds = [];
		foreach ($yesFlagIdentifiers as $yesFlagIdentifier) {
			if (isset($flagIdentifiersToIds[$yesFlagIdentifier])) {
				$yesFlagIds[] = $flagIdentifiersToIds[$yesFlagIdentifier];
			}
		}

		$noFlagIds = [];
		foreach ($noFlagIdentifiers as $noFlagIdentifier) {
			if (isset($flagIdentifiersToIds[$noFlagIdentifier])) {
				$noFlagIds[] = $flagIdentifiersToIds[$noFlagIdentifier];
			}
		}

		$pokemonId = null;
		if ($pokemonIdentifier) {
			try {
				$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
				$pokemonId = $pokemon->id;
			} catch (PokemonNotFoundException) {
			}
		}

		$includeTransferMoves = (bool) $includeTransferMoves;

		$this->moves = $this->queries->search(
			$versionGroupId,
			$typeIds,
			$categoryIds,
			$yesFlagIds,
			$noFlagIds,
			$pokemonId,
			$includeTransferMoves,
			$languageId,
		);
	}
}
