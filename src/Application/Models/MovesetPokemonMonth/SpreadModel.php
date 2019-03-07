<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use DateTime;
use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\NatureNameRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;

class SpreadModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository */
	private $movesetRatedSpreadRepository;

	/** @var BaseStatRepositoryInterface $baseStatRepository */
	private $baseStatRepository;

	/** @var NatureRepositoryInterface $natureRepository */
	private $natureRepository;

	/** @var NatureNameRepositoryInterface $natureNameRepository */
	private $natureNameRepository;

	/** @var StatCalculator $statCalculator */
	private $statCalculator;


	/** @var SpreadData[] $spreadDatas */
	private $spreadDatas = [];


	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 * @param NatureRepositoryInterface $natureRepository
	 * @param NatureNameRepositoryInterface $natureNameRepository
	 * @param StatCalculator $statCalculator
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository,
		BaseStatRepositoryInterface $baseStatRepository,
		NatureRepositoryInterface $natureRepository,
		NatureNameRepositoryInterface $natureNameRepository,
		StatCalculator $statCalculator
	) {
		$this->formatRepository = $formatRepository;
		$this->movesetRatedSpreadRepository = $movesetRatedSpreadRepository;
		$this->baseStatRepository = $baseStatRepository;
		$this->natureRepository = $natureRepository;
		$this->natureNameRepository = $natureNameRepository;
		$this->statCalculator = $statCalculator;
	}

	/**
	 * Get spread data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get the format.
		$format = $this->formatRepository->getById($formatId);
		$generationId = $format->getGenerationId();

		// Get moveset rated spread records.
		$movesetRatedSpreads = $this->movesetRatedSpreadRepository->getByMonthAndFormatAndRatingAndPokemon(
			$month,
			$format->getId(),
			$rating,
			$pokemonId
		);

		// Get the Pokémon's base stats.
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);

		// Assume the Pokémon has perfect IVs.
		$ivSpread = new StatValueContainer();
		$statIds = StatId::getByGeneration($generationId);
		$perfectIv = $this->statCalculator->getPerfectIv($generationId);
		foreach ($statIds as $statId) {
			$ivSpread->add(new StatValue($statId, $perfectIv));
		}

		// Calculate the Pokémon's stats for each spread.
		foreach ($movesetRatedSpreads as $movesetRatedSpread) {
			// Get this spread's nature's name.
			$natureName = $this->natureNameRepository->getByLanguageAndNature(
				$languageId,
				$movesetRatedSpread->getNatureId()
			);

			$nature = $this->natureRepository->getById(
				$movesetRatedSpread->getNatureId()
			);

			$evSpread = $movesetRatedSpread->getEvSpread();

			// Get this spread's calculated stats.
			if ($generationId->value() === 1 || $generationId->value() === 2) {
				// Pokémon Showdown simplifies the stat formula for gens 1 and 2.
				// The real formula takes the square root of the EV. So, we need
				// to give the formula the square of the EV from Showdown.
				$evSpread = new StatValueContainer();
				$calcEvSpread = new StatValueContainer();
				foreach ($statIds as $statId) {
					// For Special, use what was imported as Special Attack.
					$actingStatId = $statId->value() !== StatId::SPECIAL
						? $statId
						: new StatId(StatId::SPECIAL_ATTACK);
					$value = $movesetRatedSpread->getEvSpread()->get($actingStatId)->getValue();
					$evSpread->add(new StatValue($statId, $value));
					$calcEvSpread->add(new StatValue($statId, $value ** 2));
				}

				$statSpread = $this->statCalculator->all1(
					$generationId,
					$baseStats,
					$ivSpread,
					$calcEvSpread,
					$format->getLevel()
				);
			} else {
				$statSpread = $this->statCalculator->all3(
					$baseStats,
					$ivSpread,
					$evSpread,
					$format->getLevel(),
					$nature
				);
			}

			$this->spreadDatas[] = new SpreadData(
				$natureName->getName(),
				$nature->getIncreasedStatId(),
				$nature->getDecreasedStatId(),
				$evSpread,
				$movesetRatedSpread->getPercent(),
				$statSpread
			);
		}
	}

	/**
	 * Get the spread datas.
	 *
	 * @return SpreadData[]
	 */
	public function getSpreadDatas() : array
	{
		return $this->spreadDatas;
	}
}
