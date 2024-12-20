<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AdvancedMoveSearch\AdvancedMoveSearchSubmitModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AdvancedMoveSearchSubmitView
{
	public function __construct(
		private AdvancedMoveSearchSubmitModel $advancedMoveSearchSubmitModel,
		private DexFormatter $dexFormatter
	) {}

	/**
	 * Get data for the advanced move search page.
	 */
	public function getData() : ResponseInterface
	{
		$moves = $this->advancedMoveSearchSubmitModel->moves;
		$moves = $this->dexFormatter->formatDexMoves($moves);

		return new JsonResponse([
			'data' => [
				'moves' => $moves,
			]
		]);
	}
}
