<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Evolutions\EvolutionRepositoryInterface;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemNotFoundException;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;

final class DexItemModel
{
    private(set) array $item = [];
    private(set) array $evolutions = [];


    public function __construct(
        private(set) readonly VersionGroupModel $versionGroupModel,
        private readonly ItemRepositoryInterface $itemRepository,
        private readonly DexItemRepositoryInterface $dexItemRepository,
        private readonly EvolutionRepositoryInterface $evolutionRepository,
        private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
    ) {}


    /**
     * Set data for the dex items page.
     */
    public function setData(
        string $vgIdentifier,
        string $itemIdentifier,
        LanguageId $languageId,
    ): void {
        $this->item = [];
        $this->evolutions = [];

        try {
            $versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);
        } catch (VersionGroupNotFoundException) {
            return;
        }

        try {
            $item = $this->itemRepository->getByIdentifier($itemIdentifier);
        } catch (ItemNotFoundException) {
            return;
        }

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
            $dexPokemon = $this->dexPokemonRepository->getById(
                $versionGroupId,
                $evolution->evoFromId,
                $languageId,
            );

            $this->evolutions[] = [
                'icon' => $dexPokemon->icon,
                'identifier' => $dexPokemon->identifier,
                'name' => $dexPokemon->name,
            ];
        }
    }
}
