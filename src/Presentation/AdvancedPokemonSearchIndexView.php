<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AdvancedPokemonSearch\AdvancedPokemonSearchIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AdvancedPokemonSearchIndexView
{
	public function __construct(
		private AdvancedPokemonSearchIndexModel $advancedPokemonSearchIndexModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the advanced Pokémon search page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->advancedPokemonSearchIndexModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$types = $this->advancedPokemonSearchIndexModel->getTypes();
		$abilities = $this->advancedPokemonSearchIndexModel->getAbilities();
		$eggGroups = $this->advancedPokemonSearchIndexModel->getEggGroups();
		$genderRatios = $this->advancedPokemonSearchIndexModel->getGenderRatios();
		$moves = $this->advancedPokemonSearchIndexModel->getMoves();
		$stats = $this->advancedPokemonSearchIndexModel->getStats();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/pokemon",
			'text' => 'Pokemon',
		], [
			'text' => 'Advanced Search',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasTransferMoves' => $versionGroup->hasTransferMoves(),
					'hasAbilities' => $versionGroup->hasAbilities(),
					'hasBreeding' => $versionGroup->hasBreeding(),
					'hasEvYields' => $versionGroup->hasEvYields(),
					'hasEvBasedStats' => $versionGroup->hasEvBasedStats(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'types' => $types,
				'abilities' => $abilities,
				'eggGroups' => $eggGroups,
				'genderRatios' => $genderRatios,
				'moves' => $moves,
				'stats' => $stats,
			]
		]);
	}
}
