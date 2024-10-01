<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BreedingChains\BreedingChainsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class BreedingChainsView
{
	public function __construct(
		private BreedingChainsModel $breedingChainsModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the breeding chains page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->breedingChainsModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();

		$pokemon = $this->breedingChainsModel->getPokemon();
		$move = $this->breedingChainsModel->getMove();

		$chainsData = $this->breedingChainsModel->getChains();
		$chains = [];
		foreach ($chainsData as $chainId => $chain) {
			$records = [];
			foreach ($chain as $record) {
				$records[] = [
					'icon' => $record->getIcon(),
					'identifier' => $record->getIdentifier(),
					'name' => $record->getName(),
					'versionGroup' => $this->dexFormatter->formatDexVersionGroup(
						$record->getVersionGroup()
					),
					'eggGroups' => $this->dexFormatter->formatDexEggGroups(
						$record->getEggGroups(),
					),
					'genderRatio' => [
						'icon' => $record->getGenderRatio()->getIcon(),
						'description' => $record->getGenderRatio()->getDescription(),
					],
					'eggCycles' => $record->getEggCycles(),
					'stepsToHatch' => $record->getStepsToHatch(),
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
		$vgIdentifier = $versionGroup->getIdentifier();
		$pokemonIdentifier = $pokemon['identifier'];
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/pokemon",
			'text' => 'Pokémon',
		], [
			'url' => "/dex/$vgIdentifier/pokemon/$pokemonIdentifier",
			'text' => $pokemon['name'],
		], [
			'url' => "/dex/$vgIdentifier/pokemon/$pokemonIdentifier#egg-moves",
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
