<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Evolutions\EvolutionFormatter;
use Jp\Dex\Domain\Evolutions\EvolutionRepositoryInterface;
use Jp\Dex\Domain\Evolutions\EvolutionTableMethod;
use Jp\Dex\Domain\Evolutions\EvolutionTableRow;
use Jp\Dex\Domain\Evolutions\EvolutionTree;
use Jp\Dex\Domain\Evolutions\EvolutionTreeToTable;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Forms\FormRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\TextLinks\TextLinkRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexPokemonEvolutionsModel
{
	/** @var EvolutionTableRow[] $evolutionTableRows */
	private(set) array $evolutionTableRows = [];

	public function __construct(
		private readonly EvolutionRepositoryInterface $evolutionRepository,
		private readonly EvolutionFormatter $evolutionFormatter,
		private readonly FormIconRepositoryInterface $formIconRepository,
		private readonly FormRepositoryInterface $formRepository,
		private readonly TextLinkRepositoryInterface $textLinkRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly PokemonNameRepositoryInterface $pokemonNameRepository,
	) {}

	/**
	 * Set data for the dex Pokémon page's evolutions section.
	 */
	public function setData(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : void {
		$formId = new FormId($pokemonId->value());
		$baseFormIds = $this->getBaseFormIds($versionGroupId, $formId);
		$baseFormIds = $this->withSiblingFormIds($versionGroupId, $baseFormIds);
		$baseFormIds = $this->removeDuplicates($baseFormIds);

		foreach ($baseFormIds as $baseFormId) {
			// Get this base form's rows for the evolution table.
			// Add the rows to the evolution table.

			$methods = $this->getBaseMethods($versionGroupId, $baseFormId, $languageId);

			$tree = $this->createEvolutionTree(
				$versionGroupId,
				$baseFormId,
				$methods,
				$languageId,
				true,
			);

			$evolutionTreeToTable = new EvolutionTreeToTable();
			$rows = $evolutionTreeToTable->convert($tree);
			foreach ($rows as $row) {
				$this->evolutionTableRows[] = $row;
			}
		}
	}

	/**
	 * Go backward through this form's evolutionary tree to get all the forms
	 * it could have evolved from.
	 *
	 * @return FormId[]
	 */
	private function getBaseFormIds(VersionGroupId $versionGroupId, FormId $formId) : array
	{
		$prevEvos = $this->evolutionRepository->getByEvoInto($versionGroupId, $formId);
		if (!$prevEvos) {
			return [$formId];
		}

		$allBaseFormIds = [];
		foreach ($prevEvos as $prevEvo) {
			$baseFormIds = $this->getBaseFormIds($versionGroupId, $prevEvo->evoFromId);
			$allBaseFormIds = array_merge($allBaseFormIds, $baseFormIds);
		}

		return $allBaseFormIds;
	}

	/**
	 * For each of these "base" form ids (the first stage in an evolution line,
	 * such as Plant Burmy), add any "sibling" form ids it has (forms of the
	 * same Pokémon, such as Sandy Burmy and Trash Burmy).
	 *
	 * @param FormId[] $baseFormIds
	 *
	 * @return FormId[]
	 */
	private function withSiblingFormIds(VersionGroupId $versionGroupId, array $baseFormIds) : array
	{
		$allSiblingFormIds = [];
		foreach ($baseFormIds as $baseFormId) {
			$siblingFormIds = $this->getSiblingFormIds($versionGroupId, $baseFormId);
			$allSiblingFormIds = array_merge($allSiblingFormIds, $siblingFormIds);
		}

		return $allSiblingFormIds;
	}

	/**
	 * Get this form's sibling form ids.
	 */
	private function getSiblingFormIds(VersionGroupId $versionGroupId, FormId $formId) : array
	{
		$form = $this->formRepository->getById($formId);
		return $this->formRepository->getByVgAndPokemon($versionGroupId, $form->pokemonId);
	}

	/**
	 * @param FormId[] $formIds
	 *
	 * @return FormId[]
	 */
	private function removeDuplicates(array $formIds) : array
	{
		$outputIds = [];

		foreach ($formIds as $formId) {
			$outputIds[$formId->value()] = $formId;
		}

		return $outputIds;
	}

	/**
	 * Get the evolution methods for the root of the evolution tree. (Blank,
	 * except for babies who require incense.)
	 *
	 * @return EvolutionTableMethod[]
	 */
	private function getBaseMethods(
		VersionGroupId $versionGroupId,
		FormId $formId,
		LanguageId $languageId,
	) : array {
		$textLinkItem = $this->textLinkRepository->getForIncense(
			$versionGroupId,
			$languageId,
			$formId,
		);
		if (!$textLinkItem) {
			return [];
		}

		$item = $textLinkItem->getLinkHtml();
		return [
			new EvolutionTableMethod(
				"Either parent must hold $item",
			),
		];
	}

	/**
	 * Create the evolution tree that branches out from this form.
	 */
	private function createEvolutionTree(
		VersionGroupId $versionGroupId,
		FormId $formId,
		/** @var EvolutionTableMethod[] $methods */ array $methods,
		LanguageId $languageId,
		bool $isFirstStage,
	) : EvolutionTree {
		$evolutions = $this->evolutionRepository->getByEvoFrom($versionGroupId, $formId);

		$evoIntoIds = [];
		$evoMethods = [];
		foreach ($evolutions as $evolution) {
			$evoIntoId = $evolution->evoIntoId;
			$evoIntoIds[$evoIntoId->value()] = $evoIntoId;
			// We only need each evolution form once.

			$evoMethod = $this->evolutionFormatter->format($evolution, $languageId);
			$evoMethods[$evoIntoId->value()][] = $evoMethod;
			// But if there are multiple ways to evolve into that form, we still
			// want to know each of them.
		}

		$evoIntoTrees = [];
		foreach ($evoIntoIds as $evoIntoId) {
			$evoIntoTrees[] = $this->createEvolutionTree(
				$versionGroupId,
				$evoIntoId,
				$evoMethods[$evoIntoId->value()] ?? [],
				$languageId,
				false,
			);
		}

		$formIcon = $this->formIconRepository->getByVgAndFormAndFemaleAndRightAndShiny(
			$versionGroupId,
			$formId,
			false,
			false,
			false,
		);
		$form = $this->formRepository->getById($formId);
		$pokemon = $this->pokemonRepository->getById($form->pokemonId);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->getId(),
		);

		return new EvolutionTree(
			$isFirstStage,
			$formIcon->image,
			$pokemon->getIdentifier(),
			$pokemonName->getName(),
			$methods,
			$evoIntoTrees,
		);
	}
}
