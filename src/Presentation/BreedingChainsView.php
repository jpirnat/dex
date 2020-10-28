<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BreedingChains\BreedingChainRecord;
use Jp\Dex\Application\Models\BreedingChains\BreedingChainsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class BreedingChainsView
{
	public function __construct(
		private BreedingChainsModel $breedingChainsModel,
		private DexFormatter $dexFormatter,
	) {}

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
					'versionGroup' => $this->dexFormatter->formatDexVersionGroup(
						$record->getVersionGroup()
					),
					'eggGroupNames' => $record->getEggGroupNames(),
					'baseEggCycles' => $record->getBaseEggCycles(),
					'genderRatio' => [
						'icon' => $record->getGenderRatioIcon(),
						'text' => $record->getGenderRatioText(),
					],
					'moveMethod' => $record->getMoveMethod(),
				];
			}
			$chains[] = [
				'id' => $chainId,
				'pokemon' => $records,
				'show' => false,
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

		return new JsonResponse([
			'data' => [
				'title' => 'Pokémon - ' . $pokemon['name'] . ' - '. $move['name']
					. ' Breeding Chains',

				'breadcrumbs' => $breadcrumbs,

				'chains' => $chains,
			]
		]);
	}
}
