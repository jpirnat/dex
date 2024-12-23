<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Evolutions\EvolutionRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormRepositoryInterface;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;

final class DexItemModel
{
	private(set) array $item = [];
	private(set) array $evolutions = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly ItemRepositoryInterface $itemRepository,
		private readonly DexItemRepositoryInterface $dexItemRepository,
		private readonly EvolutionRepositoryInterface $evolutionRepository,
		private readonly FormRepositoryInterface $formRepository,
		private readonly FormIconRepositoryInterface $formIconRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly PokemonNameRepositoryInterface $pokemonNameRepository,
	) {}


	/**
	 * Set data for the dex items page.
	 */
	public function setData(
		string $vgIdentifier,
		string $itemIdentifier,
		LanguageId $languageId,
	) : void {
		$this->item = [];
		$this->evolutions = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		$this->versionGroupModel->setWithItem($item->id);

		$dexItem = $this->dexItemRepository->getById(
			$versionGroupId,
			$item->id,
			$languageId,
		);

		$this->item = [
			'icon' => $dexItem->icon,
			'identifier' => $dexItem->identifier,
			'name' => $dexItem->name,
			'description' => $dexItem->description,
		];

		$evolutions = $this->evolutionRepository->getByItem(
			$versionGroupId,
			$item->id,
		);
		foreach ($evolutions as $evolution) {
			$formId = $evolution->evoFromId;

			$form = $this->formRepository->getById($formId);
			$formIcon = $this->formIconRepository->getByVgAndFormAndFemaleAndRightAndShiny(
				$versionGroupId,
				$formId,
				false,
				false,
				false,
			);
			$pokemon = $this->pokemonRepository->getById($form->pokemonId);
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$pokemon->getId(),
			);

			$this->evolutions[] = [
				'icon' => $formIcon->image,
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemonName->getName(),
			];
		}
	}
}
