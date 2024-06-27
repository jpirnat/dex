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
		$versionGroupModel = $this->dexTypeModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$type = $this->dexTypeModel->getType();
		$types = $this->dexTypeModel->getTypes();

		$damageDealt = $this->dexTypeModel->getDamageDealt();
		$damageTaken = $this->dexTypeModel->getDamageTaken();

		$stats = $this->dexTypeModel->getStats();
		$pokemon = $this->dexTypeModel->getPokemon();
		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);

		$moves = $this->dexTypeModel->getMoves();
		$moves = $this->dexFormatter->formatDexMoves($moves);


		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
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
					'id' => $versionGroup->getId()->value(),
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'type' => [
					'identifier' => $type['identifier'],
				],

				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageDealt' => $damageDealt,
				'damageTaken' => $damageTaken,

				'pokemons' => $pokemon,
				'showAbilities' => $versionGroup->getId()->hasAbilities(),
				'stats' => $stats,

				'moves' => $moves,
				'showMoveDescriptions' => $versionGroup->getId()->hasMoveDescriptions(),
			]
		]);
	}
}
