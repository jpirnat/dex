<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexPokemonsModel
{
	private array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private array $pokemon = [];


	public function __construct(
		private GenerationModel $generationModel,
		private DexPokemonRepositoryInterface $dexPokemonRepository,
		private StatNameModel $statNameModel,
	) {}


	/**
	 * Set data for the dex Pokémons page.
	 */
	public function setData(
		string $generationIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$this->generationModel->setGensSince(new GenerationId(1));

		// Get stat name abbreviations.
		$this->stats = $this->statNameModel->getByGeneration($generationId, $languageId);

		$this->pokemon = $this->dexPokemonRepository->getByGeneration(
			$generationId,
			$languageId
		);
	}


	/**
	 * Get the generation model.
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the stats and their names.
	 */
	public function getStats() : array
	{
		return $this->stats;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return DexPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
