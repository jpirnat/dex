<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Application\Models\StatsAveragedPokemon\AbilityModel;
use Jp\Dex\Application\Models\StatsAveragedPokemon\ItemModel;
use Jp\Dex\Application\Models\StatsAveragedPokemon\MoveModel;
use Jp\Dex\Application\Models\StatsPokemon\PokemonModel;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class AveragedPokemonModel
{
	private(set) string $start;
	private(set) string $end;
	private(set) Format $format;
	private(set) int $rating;
	private(set) Pokemon $pokemon;
	private(set) LanguageId $languageId;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) VersionGroup $versionGroup;
	private(set) Generation $generation;

	private(set) array $abilities = [];
	private(set) array $items = [];
	private(set) array $moves = [];


	public function __construct(
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly VersionGroupRepositoryInterface $versionGroupRepository,
		private readonly GenerationRepositoryInterface $generationRepository,
		private(set) readonly PokemonModel $pokemonModel,
		private readonly AbilityModel $abilityModel,
		private readonly ItemModel $itemModel,
		private readonly MoveModel $moveModel,
	) {}


	/**
	 * Set individual Pokémon usage data averaged over multiple months.
	 */
	public function setData(
		string $start,
		string $end,
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier,
		LanguageId $languageId,
	) : void {
		$this->start = $start;
		$this->end = $end;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the start month and end month.
		$start = new DateTime("$start-01");
		$end = new DateTime("$end-01");

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		// Get the Pokémon.
		$this->pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Get the ratings for these months.
		$this->ratings = $this->ratingQueries->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId(),
		);

		// Get Pokémon data.
		$this->pokemonModel->setData(
			$this->format->getVersionGroupId(),
			$this->pokemon->getId(),
			$languageId,
		);

		// Get the format's version group and generation.
		$this->versionGroup = $this->versionGroupRepository->getById($this->format->getVersionGroupId());
		$this->generation = $this->generationRepository->getById($this->versionGroup->getGenerationId());

		// Get ability data.
		$this->abilities = $this->abilityModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);

		// Get item data.
		$this->items = $this->itemModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);

		// Get move data.
		$this->moves = $this->moveModel->setData(
			$start,
			$end,
			$this->format->getId(),
			$rating,
			$this->pokemon->getId(),
			$languageId,
		);
	}
}
