<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

interface GenerationRepositoryInterface
{
	/**
	 * Get a generation by its id.
	 *
	 * @throws GenerationNotFoundException if no generation exists with this id.
	 */
	public function getById(GenerationId $generationId) : Generation;
}
