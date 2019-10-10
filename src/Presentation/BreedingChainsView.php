<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BreedingChains\BreedingChainRecord;
use Jp\Dex\Application\Models\BreedingChains\BreedingChainsModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class BreedingChainsView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var BreedingChainsModel $breedingChainsModel */
	private $breedingChainsModel;

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
			'text' => 'Pokémon',
		], [
			'url' => "/dex/$generationIdentifier/pokemon/$pokemonIdentifier",
			'text' => $pokemon['name'],
		], [
			'url' => "/dex/$generationIdentifier/pokemon/$pokemonIdentifier#egg-moves",
			'text' => 'Breeding',
		]];

		$content = $this->renderer->render(
			'html/dex/breeding-chains.twig',
			$this->baseView->getBaseVariables() + [
				'breadcrumbs' => $breadcrumbs,
				'chains' => $chains,
			]
		);

		return new HtmlResponse($content);
	}
}
