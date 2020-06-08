<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexPokemonsModel
{
	private GenerationModel $generationModel;
	private DexPokemonRepositoryInterface $dexPokemonRepository;
	private StatNameModel $statNameModel;


	private array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private array $pokemon = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexPokemonRepositoryInterface $dexPokemonRepository
	 * @param StatNameModel $statNameModel
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexPokemonRepositoryInterface $dexPokemonRepository,
		StatNameModel $statNameModel
	) {
		$this->generationModel = $generationModel;
		$this->dexPokemonRepository = $dexPokemonRepository;
		$this->statNameModel = $statNameModel;
	}


	/**
	 * Set data for the dex Pokémons page.
	 *
	 * @param string $generationIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
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
	 *
	 * @return GenerationModel
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the stats and their names.
	 *
	 * @return array
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
