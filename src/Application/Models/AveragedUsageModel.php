<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\AveragedUsagePokemon;

final class AveragedUsageModel
{
	private(set) string $start;
	private(set) string $end;
	private(set) Format $format;
	private(set) int $rating;
	private(set) LanguageId $languageId;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) bool $showLeadsLink;

	/** @var AveragedUsagePokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly UsageAveragedPokemonRepositoryInterface $usageAveragedPokemonRepository,
		private readonly UsageRatedAveragedPokemonRepositoryInterface $usageRatedAveragedPokemonRepository,
		private readonly LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository,
		private readonly MonthsCounter $monthsCounter,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly PokemonNameRepositoryInterface $pokemonNameRepository,
		private readonly FormIconRepositoryInterface $formIconRepository,
	) {}


	/**
	 * Get usage data averaged over multiple months.
	 */
	public function setData(
		string $start,
		string $end,
		string $formatIdentifier,
		int $rating,
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

		// Get the ratings for these months.
		$this->ratings = $this->ratingQueries->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId(),
		);

		// Does leads rated data exist for these months?
		$this->showLeadsLink = $this->leadsRatedAveragedPokemonRepository->hasAny(
			$start,
			$end,
			$this->format->getId(),
			$rating,
		);

		// Get usage Pokémon records for these months.
		$usageAveragedPokemons = $this->usageAveragedPokemonRepository->getByMonthsAndFormat(
			$start,
			$end,
			$this->format->getId(),
		);

		// Get usage rated Pokémon records for these months.
		$usageRatedAveragedPokemons = $this->usageRatedAveragedPokemonRepository->getByMonthsAndFormatAndRating(
			$start,
			$end,
			$this->format->getId(),
			$rating,
		);

		// Get each Pokémon's count of months with moveset data (to determine
		// whether the moveset link should be shown).
		$monthCounts = $this->monthsCounter->countMovesetMonthsAll(
			$start,
			$end,
			$this->format->getId(),
			$rating,
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByVgAndFemaleAndRightAndShiny(
			$this->format->getVersionGroupId(),
			false,
			false,
			false,
		);

		// Get each usage record's data.
		foreach ($usageRatedAveragedPokemons as $usageRatedAveragedPokemon) {
			$pokemonId = $usageRatedAveragedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon's number of months of moveset data.
			$numberOfMonths = $monthCounts[$pokemonId->value()] ?? 0;

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for these months.
			$usageAveragedPokemon = $usageAveragedPokemons[$pokemonId->value()];

			$this->pokemon[] = new AveragedUsagePokemon(
				$usageRatedAveragedPokemon->getRank(),
				$formIcon->getImage(),
				$numberOfMonths,
				$pokemon->getIdentifier(),
				$pokemonName->getName(),
				$usageRatedAveragedPokemon->getUsagePercent(),
				$usageAveragedPokemon->getRaw(),
				$usageAveragedPokemon->getRawPercent(),
				$usageAveragedPokemon->getReal(),
				$usageAveragedPokemon->getRealPercent(),
			);
		}
	}
}
