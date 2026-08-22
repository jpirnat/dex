<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class IndexView
{
    public function __construct(
        private RendererInterface $renderer,
        private BaseView $baseView,
    ) {}

    /**
     * Show the home page.
     */
    public function index(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/index.twig',
            $this->baseView->getBaseVariables()
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the About page.
     */
    public function about(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/about.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'About',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex abilities page.
     */
    public function dexAbilities(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/abilities.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Abilities',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex ability page.
     */
    public function dexAbility(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/ability.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Abilities',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex ability flag page.
     */
    public function dexAbilityFlag(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/ability-flag.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Ability Flags',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex egg groups page.
     */
    public function dexEggGroups(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/egg-groups.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Egg Groups',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex egg group page.
     */
    public function dexEggGroup(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/egg-group.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Egg Groups',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex index page.
     */
    public function dexIndex(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/index.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Dex',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex items page.
     */
    public function dexItems(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/items.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Items',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex item page.
     */
    public function dexItem(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/item.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Item',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex moves page.
     */
    public function dexMoves(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/moves.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Moves',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex move page.
     */
    public function dexMove(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/move.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Move',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex move flag page.
     */
    public function dexMoveFlag(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/move-flag.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Move Flags',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex natures page.
     */
    public function dexNatures(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/natures.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Natures',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex Pokémons page.
     */
    public function dexPokemons(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/pokemons.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Pokémon',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex Pokémon page.
     */
    public function dexPokemon(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/pokemon.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Pokémon',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the breeding chains page.
     */
    public function breedingChains(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/breeding-chains.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Pokémon',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex TMs page.
     */
    public function dexTms(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/tms.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'TMs',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex types page.
     */
    public function dexTypes(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/types.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Types',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the dex type page.
     */
    public function dexType(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/type.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Types',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the advanced Pokémon search page.
     */
    public function advancedPokemonSearch(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/advanced-pokemon-search.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Pokémon - Advanced Search',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the advanced move search page.
     */
    public function advancedMoveSearch(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/advanced-move-search.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Moves - Advanced Search',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the IV calculator page.
     */
    public function ivCalculator(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/tools/iv-calculator.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'IV Calculator',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the EV calculator page.
     */
    public function evCalculator(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/tools/ev-calculator.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'EV Calculator',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stat calculator page.
     */
    public function statCalculator(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/dex/tools/stat-calculator.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Stat Calculator',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats index page.
     */
    public function battleDataIndex(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/index.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats month page.
     */
    public function battleDataMonth(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/month.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats usage page.
     */
    public function battleDataUsage(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/usage.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats leads page.
     */
    public function battleDataLeads(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/leads.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats Pokémon page.
     */
    public function battleDataPokemon(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/pokemon.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats averaged usage page.
     */
    public function battleDataAveragedUsage(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/averaged-usage.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats averaged leads page.
     */
    public function battleDataAveragedLeads(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/averaged-leads.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats averaged Pokémon page.
     */
    public function battleDataAveragedPokemon(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/averaged-pokemon.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats ability page.
     */
    public function battleDataAbility(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/ability.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats item page.
     */
    public function battleDataItem(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/item.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats move page.
     */
    public function battleDataMove(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/move.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the stats chart page.
     */
    public function battleDataChart(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/battle-data/chart.twig',
            $this->baseView->getBaseVariables() + [
                'title' => 'Battle Data - Chart',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the 404 page.
     */
    public function error404(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/404.twig',
            $this->baseView->getBaseVariables() + [
                'title' => '404 Not Found',
            ]
        );

        return new HtmlResponse($content);
    }

    /**
     * Show the error page.
     */
    public function error(): ResponseInterface
    {
        $content = $this->renderer->render(
            'html/error.twig',
            $this->baseView->getBaseVariables() + [
                'title' => '404 Not Found',
            ]
        );

        return new HtmlResponse($content);
    }
}
