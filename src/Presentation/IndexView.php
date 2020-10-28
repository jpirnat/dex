<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class IndexView
{
	public function __construct(
		private RendererInterface $renderer,
		private BaseView $baseView,
	) {}

	/**
	 * Show the home page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/index.twig',
			$this->baseView->getBaseVariables()
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex abilities page.
	 *
	 * @return ResponseInterface
	 */
	public function dexAbilities() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function dexAbility() : ResponseInterface
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
	 * Show the dex moves page.
	 *
	 * @return ResponseInterface
	 */
	public function dexMoves() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function dexMove() : ResponseInterface
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
	 * Show the dex natures page.
	 *
	 * @return ResponseInterface
	 */
	public function dexNatures() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function dexPokemons() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function dexPokemon() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function breedingChains() : ResponseInterface
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
	 * Show the dex types page.
	 *
	 * @return ResponseInterface
	 */
	public function dexTypes() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function dexType() : ResponseInterface
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
	 * Show the stats index page.
	 *
	 * @return ResponseInterface
	 */
	public function statsIndex() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/index.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats month page.
	 *
	 * @return ResponseInterface
	 */
	public function statsMonth() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/month.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats usage page.
	 *
	 * @return ResponseInterface
	 */
	public function statsUsage() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/usage.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats leads page.
	 *
	 * @return ResponseInterface
	 */
	public function statsLeads() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/leads.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats Pokémon page.
	 *
	 * @return ResponseInterface
	 */
	public function statsPokemon() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/pokemon.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats ability page.
	 *
	 * @return ResponseInterface
	 */
	public function statsAbility() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/ability.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats item page.
	 *
	 * @return ResponseInterface
	 */
	public function statsItem() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/item.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats move page.
	 *
	 * @return ResponseInterface
	 */
	public function statsMove() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/move.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats chart page.
	 *
	 * @return ResponseInterface
	 */
	public function statsChart() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/chart.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats - Chart',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the 404 page.
	 *
	 * @return ResponseInterface
	 */
	public function error404() : ResponseInterface
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
	 *
	 * @return ResponseInterface
	 */
	public function error() : ResponseInterface
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
