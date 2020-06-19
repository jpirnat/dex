<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BreedingChains\BreedingChainRecord;
use Jp\Dex\Application\Models\BreedingChains\BreedingChainsModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class BreedingChainsView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private BreedingChainsModel $breedingChainsModel;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param BreedingChainsModel $breedingChainsModel
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		BreedingChainsModel $breedingChainsModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->breedingChainsModel = $breedingChainsModel;
	}

	/**
	 * Show the breeding chains page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->breedingChainsModel->getGenerationModel();
		$generation = $generationModel->getGeneration();

		$pokemon = $this->breedingChainsModel->getPokemon();
		$move = $this->breedingChainsModel->getMove();

		$chainsData = $this->breedingChainsModel->getChains();
		$chains = [];
		foreach ($chainsData as $chainId => $chain) {
			$records = [];
			/** @var BreedingChainRecord $record */
			foreach ($chain as $record) {
				$records[] = [
					'icon' => $record->getFormIcon(),
					'generationIdentifier' => $record->getGenerationIdentifier(),
					'identifier' => $record->getPokemonIdentifier(),
					'name' => $record->getPokemonName(),
					'versionGroupIcon' => $record->getVersionGroupIcon(),
					'eggGroupNames' => $record->getEggGroupNames(),
					'baseEggCycles' => $record->getBaseEggCycles(),
					'genderRatioIcon' => $record->getGenderRatioIcon(),
					'moveMethod' => $record->getMoveMethod(),
				];
			}
			$chains[] = [
				'id' => $chainId,
				'pokemon' => $records,
			];
		}

		// Navigational breadcrumbs.
		$generationIdentifier = $generation->getIdentifier();
		$pokemonIdentifier = $pokemon['identifier'];
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'url' => "/dex/$generationIdentifier/pokemon",
			'text' => 'Pokémon',
		], [
			'url' => "/dex/$generationIdentifier/pokemon/$pokemonIdentifier",
			'text' => $pokemon['name'],
		], [
			'url' => "/dex/$generationIdentifier/pokemon/$pokemonIdentifier#egg-moves",
			'text' => 'Egg Moves',
		], [
			'text' => $move['name'] . ' Breeding Chains',
		]];

		$content = $this->renderer->render(
			'html/dex/breeding-chains.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Pokémon - ' . $pokemon['name'] . ' - ' . $move['name'] . ' Breeding Chains',
				'breadcrumbs' => $breadcrumbs,
				'chains' => $chains,
			]
		);

		return new HtmlResponse($content);
	}
}
