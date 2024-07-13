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
	private array $item = [];
	private array $evolutions = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private ItemRepositoryInterface $itemRepository,
		private DexItemRepositoryInterface $dexItemRepository,

		private EvolutionRepositoryInterface $evolutionRepository,
		private FormRepositoryInterface $formRepository,
		private FormIconRepositoryInterface $formIconRepository,
		private PokemonRepositoryInterface $pokemonRepository,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
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
		$itemId = $item->getId();

		$this->versionGroupModel->setWithItem($itemId);

		$dexItem = $this->dexItemRepository->getById(
			$versionGroupId,
			$itemId,
			$languageId,
		);

		$this->item = [
			'icon' => $dexItem->getIcon(),
			'identifier' => $dexItem->getIdentifier(),
			'name' => $dexItem->getName(),
			'description' => $dexItem->getDescription(),
		];

		$evolutions = $this->evolutionRepository->getByItem(
			$versionGroupId,
			$itemId,
		);
		foreach ($evolutions as $evolution) {
			$formId = $evolution->getEvoFromId();

			$form = $this->formRepository->getById($formId);
			$formIcon = $this->formIconRepository->getByVgAndFormAndFemaleAndRightAndShiny(
				$versionGroupId,
				$formId,
				false,
				false,
				false,
			);
			$pokemon = $this->pokemonRepository->getById($form->getPokemonId());
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$pokemon->getId(),
			);

			$this->evolutions[] = [
				'icon' => $formIcon->getImage(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemonName->getName(),
			];
		}
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getItem() : array
	{
		return $this->item;
	}

	public function getEvolutions() : array
	{
		return $this->evolutions;
	}
}
