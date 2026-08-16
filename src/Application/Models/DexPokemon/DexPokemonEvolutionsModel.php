<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Evolutions\EvolutionFormatter;
use Jp\Dex\Domain\Evolutions\EvolutionRepositoryInterface;
use Jp\Dex\Domain\Evolutions\EvolutionTableMethod;
use Jp\Dex\Domain\Evolutions\EvolutionTableRow;
use Jp\Dex\Domain\Evolutions\EvolutionTree;
use Jp\Dex\Domain\Evolutions\EvolutionTreeToTable;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\TextLinks\TextLinkRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexPokemonEvolutionsModel
{
    /** @var EvolutionTableRow[] $evolutionTableRows */
    private(set) array $evolutionTableRows = [];

    public function __construct(
        private readonly EvolutionRepositoryInterface $evolutionRepository,
        private readonly EvolutionFormatter $evolutionFormatter,
        private readonly TextLinkRepositoryInterface $textLinkRepository,
        private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
    ) {}

    /**
     * Set data for the dex Pokémon page's evolutions section.
     */
    public function setData(
        VersionGroupId $versionGroupId,
        PokemonId $pokemonId,
        LanguageId $languageId,
    ): void {
        $basePokemonIds = $this->getBasePokemonIds($versionGroupId, $pokemonId);
        $basePokemonIds = $this->removeDuplicates($basePokemonIds);

        foreach ($basePokemonIds as $basePokemonId) {
            // Get this base form's rows for the evolution table.
            // Add the rows to the evolution table.

            $methods = $this->getBaseMethods($versionGroupId, $basePokemonId, $languageId);

            $tree = $this->createEvolutionTree(
                $versionGroupId,
                $basePokemonId,
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
     * Go backward through this form's evolutionary tree to get all the Pokémon
     * it could have evolved from.
     * (The branching aspect only really matters for Gimmighoul.)
     *
     * @return PokemonId[]
     */
    private function getBasePokemonIds(VersionGroupId $versionGroupId, PokemonId $pokemonId): array
    {
        $prevEvos = $this->evolutionRepository->getByEvoInto($versionGroupId, $pokemonId);
        if (!$prevEvos) {
            return [$pokemonId];
        }

        $allBaseFormIds = [];
        foreach ($prevEvos as $prevEvo) {
            $baseFormIds = $this->getBasePokemonIds($versionGroupId, $prevEvo->evoFromId);
            $allBaseFormIds = array_merge($allBaseFormIds, $baseFormIds);
        }

        return $allBaseFormIds;
    }

    /**
     * @param PokemonId[] $pokemonIds
     *
     * @return PokemonId[]
     */
    private function removeDuplicates(array $pokemonIds): array
    {
        $outputIds = [];

        foreach ($pokemonIds as $pokemonId) {
            $pId = $pokemonId->value;
            $outputIds[$pId] = $pokemonId;
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
        PokemonId $pokemonId,
        LanguageId $languageId,
    ): array {
        $textLinkItem = $this->textLinkRepository->getForIncense(
            $versionGroupId,
            $languageId,
            $pokemonId,
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
        PokemonId $pokemonId,
        /** @var EvolutionTableMethod[] $methods */ array $methods,
        LanguageId $languageId,
        bool $isFirstStage,
    ): EvolutionTree {
        $evolutions = $this->evolutionRepository->getByEvoFrom($versionGroupId, $pokemonId);

        $evoIntoIds = [];
        $evoMethods = [];
        foreach ($evolutions as $evolution) {
            $evoIntoIdValue = $evolution->evoIntoId->value;
            $evoIntoIds[$evoIntoIdValue] = $evolution->evoIntoId;
            // We only need each evolution form once.

            $evoMethod = $this->evolutionFormatter->format($evolution, $languageId);
            $evoMethods[$evoIntoIdValue][] = $evoMethod;
            // But if there are multiple ways to evolve into that form, we still
            // want to know each of them.
        }

        $evoIntoTrees = [];
        foreach ($evoIntoIds as $evoIntoId) {
            $evoIntoTrees[] = $this->createEvolutionTree(
                $versionGroupId,
                $evoIntoId,
                $evoMethods[$evoIntoId->value] ?? [],
                $languageId,
                false,
            );
        }

        $dexPokemon = $this->dexPokemonRepository->getById(
            $versionGroupId,
            $pokemonId,
            $languageId,
        );

        return new EvolutionTree(
            $isFirstStage,
            $dexPokemon->icon,
            $dexPokemon->identifier,
            $dexPokemon->name,
            $methods,
            $evoIntoTrees,
        );
    }
}
