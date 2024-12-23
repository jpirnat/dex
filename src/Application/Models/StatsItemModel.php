<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsItemPokemon;
use Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class StatsItemModel
{
	private(set) string $month;
	private(set) Format $format;
	private(set) int $rating;
	private(set) array $item;
	private(set) LanguageId $languageId;
	private(set) VersionGroup $versionGroup;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) string $speedName = '';

	/** @var StatsItemPokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private(set) readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly ItemRepositoryInterface $itemRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly DexItemRepositoryInterface $dexItemRepository,
		private readonly StatNameRepositoryInterface $statNameRepository,
		private readonly StatsItemPokemonRepositoryInterface $statsItemPokemonRepository,
	) {}


	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $itemIdentifier,
		LanguageId $languageId,
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->item = [];
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		$this->versionGroup = $this->vgRepository->getById(
			$this->format->versionGroupId
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->id);
		$thisMonth = $this->dateModel->thisMonth;
		$prevMonth = $this->dateModel->prevMonth;

		// Get the item.
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->id,
		);

		$dexItem = $this->dexItemRepository->getById(
			$this->format->versionGroupId,
			$item->id,
			$languageId,
		);
		$this->item = [
			'icon' => $dexItem->icon,
			'identifier' => $dexItem->identifier,
			'name' => $dexItem->name,
			'description' => $dexItem->description,
		];

		$speedName = $this->statNameRepository->getByLanguageAndStat(
			$languageId,
			new StatId(StatId::SPEED),
		);
		$this->speedName = $speedName->name;

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsItemPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->id,
			$rating,
			$item->id,
			$languageId,
		);
	}
}
