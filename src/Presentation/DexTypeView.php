<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypeModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexTypeView
{
	public function __construct(
		private DexTypeModel $dexTypeModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex type page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexTypeModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$type = $this->dexTypeModel->type;
		$types = $this->dexTypeModel->types;

		$damageDealt = $this->dexTypeModel->damageDealt;
		$damageTaken = $this->dexTypeModel->damageTaken;

		$stats = $this->dexTypeModel->stats;
		$pokemon = $this->dexTypeModel->pokemon;
		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);

		$moves = $this->dexTypeModel->moves;
		$moves = $this->dexFormatter->formatDexMoves($moves);


		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/types",
			'text' => 'Types',
		], [
			'text' => $type['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Types - ' . $type['name'],

				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasAbilities' => $versionGroup->hasAbilities(),
					'hasBreeding' => $versionGroup->hasBreeding(),
					'hasEvYields' => $versionGroup->hasEvYields(),
					'hasEvBasedStats' => $versionGroup->hasEvBasedStats(),
					'hasMoveDescriptions' => $versionGroup->getId()->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'type' => $type,

				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageDealt' => $damageDealt,
				'damageTaken' => $damageTaken,

				'pokemons' => $pokemon,
				'stats' => $stats,

				'moves' => $moves,
			]
		]);
	}
}
