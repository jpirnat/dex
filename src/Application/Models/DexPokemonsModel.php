<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexPokemonsModel
{
	private GenerationModel $generationModel;
	private DexPokemonRepositoryInterface $dexPokemonRepository;
	private StatNameRepositoryInterface $statNameRepository;


	/** @var string[] $statAbbreviations */
	private array $statAbbreviations = [];

	/** @var DexPokemon[] $pokemon */
	private array $pokemon = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param DexPokemonRepositoryInterface $dexPokemonRepository
	 * @param StatNameRepositoryInterface $statNameRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		DexPokemonRepositoryInterface $dexPokemonRepository,
		StatNameRepositoryInterface $statNameRepository
	) {
		$this->generationModel = $generationModel;
		$this->dexPokemonRepository = $dexPokemonRepository;
		$this->statNameRepository = $statNameRepository;
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
		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$this->statAbbreviations = [];
		$statIds = StatId::getByGeneration($generationId);
		foreach ($statIds as $statId) {
			$this->statAbbreviations[] = $statNames[$statId->value()]->getAbbreviation();
		}

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
	 * Get the stat abbreviations.
	 *
	 * @return string[]
	 */
	public function getStatAbbreviations() : array
	{
		return $this->statAbbreviations;
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
