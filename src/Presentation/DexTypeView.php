<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypeModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexTypeView
{
	public function __construct(
		private DexTypeModel $dexTypeModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Show the dex type page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexTypeModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$type = $this->dexTypeModel->getType();
		$types = $this->dexTypeModel->getTypes();

		$damageDealt = $this->dexTypeModel->getDamageDealt();
		$damageTaken = $this->dexTypeModel->getDamageTaken();

		$stats = $this->dexTypeModel->getStats();
		$showAbilities = $generation->getId()->value() >= 3;
		$pokemon = $this->dexTypeModel->getPokemon();
		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);

		$showMoveDescriptions = $generation->getId()->value() >= 3;
		$moves = $this->dexTypeModel->getMoves();
		$moves = $this->dexFormatter->formatDexMoves($moves);


		// Navigational breadcrumbs.
		$generationIdentifier = $generation->getIdentifier();
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'url' => "/dex/$generationIdentifier/types",
			'text' => 'Types',
		], [
			'text' => $type['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Types - ' . $type['name'],

				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),

				'type' => [
					'identifier' => $type['identifier'],
				],

				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageDealt' => $damageDealt,
				'damageTaken' => $damageTaken,

				'pokemons' => $pokemon,
				'showAbilities' => $showAbilities,
				'stats' => $stats,

				'moves' => $moves,
				'showMoveDescriptions' => $showMoveDescriptions,
			]
		]);
	}
}
