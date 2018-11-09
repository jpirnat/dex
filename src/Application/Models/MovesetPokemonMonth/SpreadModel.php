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

		// Get moveset rated spread records.
		$movesetRatedSpreads = $this->movesetRatedSpreadRepository->getByMonthAndFormatAndRatingAndPokemon(
			$month,
			$format->getId(),
			$rating,
			$pokemonId
		);

		// Get the Pokémon's base stats.
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$format->getGenerationId(),
			$pokemonId
		);

		// Assume the Pokémon has perfect IVs.
		$ivSpread = new StatValueContainer();
		$ivSpread->add(new StatValue(new StatId(StatId::HP), 31));
		$ivSpread->add(new StatValue(new StatId(StatId::ATTACK), 31));
		$ivSpread->add(new StatValue(new StatId(StatId::DEFENSE), 31));
		$ivSpread->add(new StatValue(new StatId(StatId::SPECIAL_ATTACK), 31));
		$ivSpread->add(new StatValue(new StatId(StatId::SPECIAL_DEFENSE), 31));
		$ivSpread->add(new StatValue(new StatId(StatId::SPEED), 31));

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

			$statSpread = $this->statCalculator->all3(
				$baseStats,
				$ivSpread,
				$movesetRatedSpread->getEvSpread(),
				$format->getLevel(),
				$nature
			);

			$this->spreadDatas[] = new SpreadData(
				$natureName->getName(),
				$nature->getIncreasedStatId(),
				$nature->getDecreasedStatId(),
				$movesetRatedSpread->getEvSpread(),
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
