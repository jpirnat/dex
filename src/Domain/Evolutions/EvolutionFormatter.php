<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\Conditions\ConditionId;
use Jp\Dex\Domain\Conditions\ConditionNameRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Natures\DexNatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\TextLinks\TextLinkRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionNameRepositoryInterface;

final readonly class EvolutionFormatter
{
	public function __construct(
		private VersionGroupRepositoryInterface $versionGroupRepository,
		private TextLinkRepositoryInterface $textLinkRepository,
		private StatNameRepositoryInterface $statNameRepository,
		private VersionNameRepositoryInterface $versionNameRepository,
		private ConditionNameRepositoryInterface $conditionNameRepository,
		private DexNatureRepositoryInterface $dexNatureRepository,
	) {}

	/**
	 * Format in words the method through which this evolution is triggered.
	 * Examples: "Level 15", "Trade", "Use Fire Stone".
	 */
	public function format(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$evoMethodId = $evolution->getEvoMethodId();

		$level = $evolution->getLevel();

		$friendship = 0;
		if ($evoMethodId->needsFriendship()) {
			$versionGroup = $this->versionGroupRepository->getById($evolution->getVersionGroupId());
			$friendship = $this->getFriendship($versionGroup->getGenerationId());
		}

		$item = '';
		if ($evoMethodId->needsItem()) {
			$textLinkItem = $this->textLinkRepository->getForItem(
				$evolution->getVersionGroupId(),
				$languageId,
				$evolution->getItemId(),
			);
			$item = $textLinkItem->getLinkHtml();
		}

		$move = '';
		if ($evoMethodId->needsMove()) {
			$textLinkMove = $this->textLinkRepository->getForMove(
				$evolution->getVersionGroupId(),
				$languageId,
				$evolution->getMoveId(),
			);
			$move = $textLinkMove->getLinkHtml();
		}

		$pokemon = '';
		if ($evoMethodId->needsPokemon()) {
			$textLinkPokemon = $this->textLinkRepository->getForPokemon(
				$evolution->getVersionGroupId(),
				$languageId,
				$evolution->getPokemonId(),
			);
			$pokemon = $textLinkPokemon->getLinkHtml();
		}

		$type = '';
		if ($evoMethodId->needsType()) {
			$textLinkType = $this->textLinkRepository->getForType(
				$evolution->getVersionGroupId(),
				$languageId,
				$evolution->getTypeId(),
			);
			$type = $textLinkType->getLinkHtml();
		}

		$version = '';
		if ($evoMethodId->needsVersion()) {
			$version = $this->versionNameRepository->getByLanguageAndVersion(
				$languageId,
				$evolution->getVersionId(),
			);
			$version = $version->getName();
		}

		$otherParameter = $evolution->getOtherParameter();

		return match ($evoMethodId->value()) {
			EvoMethodId::LEVEL_UP_FRIENDSHIP => new EvolutionTableMethod(
				"Level up, with at least $friendship friendship",
			),
			EvoMethodId::LEVEL_UP_FRIENDSHIP_MORNING => new EvolutionTableMethod(
				"Level up, during the day, with at least $friendship friendship",
			),
			EvoMethodId::LEVEL_UP_FRIENDSHIP_NIGHT => new EvolutionTableMethod(
				"Level up, during the night, with at least $friendship friendship",
			),
			EvoMethodId::LEVEL_UP, EvoMethodId::LEVEL_UP_NINJASK => new EvolutionTableMethod(
				"Level up, starting at level $level",
			),
			EvoMethodId::TRADE => new EvolutionTableMethod(
				"Trade",
			),
			EvoMethodId::TRADE_HELD_ITEM => new EvolutionTableMethod(
				"Trade, while holding $item",
			),
			EvoMethodId::TRADE_SHELMET_KARRABLAST => $this->tradeShelmetKarrablast($evolution, $languageId),
			EvoMethodId::USE_ITEM => new EvolutionTableMethod(
				"Use $item",
			),
			EvoMethodId::LEVEL_UP_ATK_GT_DEF,
			EvoMethodId::LEVEL_UP_ATK_EQ_DEF,
			EvoMethodId::LEVEL_UP_DEF_GT_ATK => $this->tyrogue($evolution, $languageId),
			EvoMethodId::LEVEL_UP_EC_LT_FIVE,
			EvoMethodId::LEVEL_UP_EC_GT_EQ_FIVE => new EvolutionTableMethod(
				"Level up, starting at level $level, 50% chance",
			),
			EvoMethodId::LEVEL_UP_SHEDINJA => $this->levelUpShedinja($evolution, $languageId),
			EvoMethodId::LEVEL_UP_BEAUTY => $this->levelUpBeauty($evolution, $languageId),
			EvoMethodId::USE_ITEM_MALE => new EvolutionTableMethod(
				"Use $item, males only",
			),
			EvoMethodId::USE_ITEM_FEMALE => new EvolutionTableMethod(
				"Use $item, female only",
			),
			EvoMethodId::LEVEL_UP_HELD_ITEM_DAY => new EvolutionTableMethod(
				"Level up, during the day, while holding $item",
			),
			EvoMethodId::LEVEL_UP_HELD_ITEM_NIGHT => new EvolutionTableMethod(
				"Level up, during the night, while holding $item",
			),
			EvoMethodId::LEVEL_UP_KNOW_MOVE => new EvolutionTableMethod(
				"Level up, knowing $move",
			),
			EvoMethodId::LEVEL_UP_WITH_TEAMMATE => new EvolutionTableMethod(
				"Level up, with $pokemon in the party",
			),
			EvoMethodId::LEVEL_UP_MALE => new EvolutionTableMethod(
				"Level up, males only, starting at level $level",
			),
			EvoMethodId::LEVEL_UP_FEMALE, EvoMethodId::LEVEL_UP_FORM_FEMALE_1 => new EvolutionTableMethod(
				"Level up, females only, starting at level $level",
			),
			EvoMethodId::LEVEL_UP_ELECTRIC => new EvolutionTableMethod(
				"Level up, around a special magnetic field",
			),
			EvoMethodId::LEVEL_UP_FOREST => new EvolutionTableMethod(
				"Level up near a Moss Rock",
			),
			EvoMethodId::LEVEL_UP_COLD => new EvolutionTableMethod(
				"Level up near an Ice Rock",
			),
			EvoMethodId::LEVEL_UP_INVERTED => new EvolutionTableMethod(
				"Level up, starting at $level, while the game system is held upside-down",
			),
			EvoMethodId::LEVEL_UP_AFFECTION_50_MOVE_TYPE => $this->levelUpAffection50MoveType($evolution, $type),
			EvoMethodId::LEVEL_UP_MOVE_TYPE => new EvolutionTableMethod(
				"Level up, starting at level $level, with a $type-type Pokémon in the party",
			),
			EvoMethodId::LEVEL_UP_WEATHER => $this->levelUpWeather($evolution),
			EvoMethodId::LEVEL_UP_MORNING => new EvolutionTableMethod(
				"Level up, during the day, starting at level $level",
			),
			EvoMethodId::LEVEL_UP_NIGHT => new EvolutionTableMethod(
				"Level up, during the night, starting at level $level",
			),
			EvoMethodId::LEVEL_UP_VERSION => new EvolutionTableMethod(
				"Level up, starting at level $level, in $version only",
			),
			EvoMethodId::LEVEL_UP_VERSION_DAY => new EvolutionTableMethod(
				"Level up, during the day, starting at level $level, in $version only",
			),
			EvoMethodId::LEVEL_UP_VERSION_NIGHT =>new EvolutionTableMethod(
				"Level up, during the night, starting at level $level, in $version only",
			),
			EvoMethodId::LEVEL_UP_SUMMIT => new EvolutionTableMethod(
				"Level up at Mount Lanakila",
			),
			EvoMethodId::LEVEL_UP_DUSK => $this->levelUpDusk($evolution),
			EvoMethodId::LEVEL_UP_WORMHOLE => new EvolutionTableMethod(
				"Level up, starting at level $level, while in an Ultra Wormhole",
			),
			EvoMethodId::USE_ITEM_WORMHOLE => new EvolutionTableMethod(
				"Use $item, while in an Ultra Wormhole",
			),
			EvoMethodId::CRITICAL_HITS_IN_BATTLE =>new EvolutionTableMethod(
				"Land $otherParameter critical hits in one battle",
			),
			EvoMethodId::HP_LOST_IN_BATTLE => $this->hpLostInBattle($evolution, $languageId),
			EvoMethodId::SPIN => $this->spin($evolution, $item),
			EvoMethodId::LEVEL_UP_NATURE_AMPED,
			EvoMethodId::LEVEL_UP_NATURE_LOW_KEY => $this->levelUpNatures($evolution, $languageId),
			EvoMethodId::TOWER_OF_DARKNESS => new EvolutionTableMethod(
				"Read the Scroll of Darkness in the Tower of Darkness",
			),
			EvoMethodId::TOWER_OF_WATERS => new EvolutionTableMethod(
				"Read the Scroll of Waters in the Tower of Waters",
			),
			EvoMethodId::LEVEL_UP_WALK_STEPS_WITH => new EvolutionTableMethod(
				"Level up, while outside of its Poké Ball after walking $otherParameter steps using the Let's Go! feature",
			),
			EvoMethodId::LEVEL_UP_UNION_CIRCLE => new EvolutionTableMethod(
				"Level up, starting at level $level, while in a Union Circle group",
			),
			EvoMethodId::LEVEL_UP_IN_BATTLE_EC_25 => new EvolutionTableMethod(
				"Level up, starting at level $level, 1% chance",
			),
			EvoMethodId::LEVEL_UP_IN_BATTLE_EC_ELSE => new EvolutionTableMethod(
				"Level up, starting at level $level, 99% chance",
			),
			EvoMethodId::LEVEL_UP_COLLECT_999 => new EvolutionTableMethod(
				"Level up, with $otherParameter Gimmighoul Coins in your bag",
			),
			EvoMethodId::LEVEL_UP_DEFEAT_EQUALS => $this->levelUpDefeatEquals($evolution, $languageId),
			EvoMethodId::LEVEL_UP_USE_MOVE_SPECIAL => new EvolutionTableMethod(
				"Level up, after using $move $otherParameter times",
			),
			EvoMethodId::LEVEL_UP_KNOW_MOVE_EC_ELSE => new EvolutionTableMethod(
				"Level up, knowing $move, 99% chance",
			),
			EvoMethodId::LEVEL_UP_KNOW_MOVE_EC_25 => new EvolutionTableMethod(
				"Level up, knowing $move, 1% chance",
			),
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_MALE,
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_FEMALE => $this->levelUpRecoilDamage($evolution, $languageId),
			EvoMethodId::USE_ITEM_FULL_MOON => new EvolutionTableMethod(
				"Use $item during a full moon",
			),
			EvoMethodId::USE_MOVE_AGILE_STYLE => $this->useMoveAgileStyle($evolution, $languageId),
			EvoMethodId::USE_MOVE_STRONG_STYLE => $this->useMoveStrongStyle($evolution, $languageId),
			EvoMethodId::USE_ITEM_DAY => new EvolutionTableMethod(
				"Use $item during the day",
			),
			EvoMethodId::USE_ITEM_NIGHT => new EvolutionTableMethod(
				"Use $item during the night",
			),
		};
	}

	private function tradeShelmetKarrablast(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$inExchangeFor = match ($evolution->getEvoFromId()->value()) {
			FormId::KARRABLAST => FormId::SHELMET,
			FormId::SHELMET => FormId::KARRABLAST,
		};

		$textLinkPokemon = $this->textLinkRepository->getForPokemon(
			$evolution->getVersionGroupId(),
			$languageId,
			new PokemonId($inExchangeFor),
		);
		$pokemon = $textLinkPokemon->getLinkHtml();

		return new EvolutionTableMethod(
			"Trade, in exchange for $pokemon",
		);
	}

	/**
	 * For Tyrogue into Hitmonlee, Hitmonchan, or Hitmontop.
	 */
	private function tyrogue(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$level = $evolution->getLevel();

		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$attack = $statNames[StatId::ATTACK]->getName();
		$defense = $statNames[StatId::DEFENSE]->getName();

		$html = match ($evolution->getEvoMethodId()->value()) {
			EvoMethodId::LEVEL_UP_ATK_GT_DEF => "Level up, starting at level $level, when $attack > $defense",
			EvoMethodId::LEVEL_UP_ATK_EQ_DEF => "Level up, starting at level $level, when $attack = $defense",
			EvoMethodId::LEVEL_UP_DEF_GT_ATK => "Level up, starting at level $level, when $attack < $defense",
		};

		return new EvolutionTableMethod(
			$html,
		);
	}

	/**
	 * For Nincada into Ninjask.
	 */
	private function levelUpShedinja(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$nincada = $this->textLinkRepository->getForPokemon(
			$evolution->getVersionGroupId(),
			$languageId,
			new PokemonId($evolution->getEvoFromId()->value()),
		);
		$ninjask = $this->textLinkRepository->getForPokemon(
			$evolution->getVersionGroupId(),
			$languageId,
			New PokemonId(PokemonId::NINJASK),
		);
		$pokeBall = $this->textLinkRepository->getForItem(
			$evolution->getVersionGroupId(),
			$languageId,
			new ItemId(ItemId::POKE_BALL),
		);

		$nincada = $nincada->getLinkHtml();
		$ninjask = $ninjask->getLinkHtml();
		$pokeBall = $pokeBall->getLinkHtml();

		return new EvolutionTableMethod(
			"Evolve $nincada into $ninjask, with an empty party slot and a $pokeBall in your bag",
		);
	}

	/**
	 * Mainly for Feebas into Milotic.
	 */
	private function levelUpBeauty(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$number = $evolution->getOtherParameter();

		$beauty = $this->conditionNameRepository->getByLanguageAndCondition(
			$languageId,
			new ConditionId(ConditionId::BEAUTY),
		);
		$beauty = $beauty->getName();

		return new EvolutionTableMethod(
			"Level up, with at least $number $beauty",
		);
	}

	/**
	 * For Eevee into Sylveon.
	 */
	private function levelUpAffection50MoveType(
		Evolution $evolution,
		string $type,
	) : EvolutionTableMethod {
		$versionGroup = $this->versionGroupRepository->getById($evolution->getVersionGroupId());

		$friendship = $this->getFriendship($versionGroup->getGenerationId());
		$friendshipOrAffection = match ($versionGroup->getGenerationId()->value()) {
			6, 7 => "at least 2 affection",
			default => "at least $friendship friendship",
		};

		return new EvolutionTableMethod(
			"Level up, with $friendshipOrAffection, while knowing a $type-type move",
		);
	}

	/**
	 * For Sliggoo into Goodra.
	 */
	private function levelUpWeather(
		Evolution $evolution,
	) : EvolutionTableMethod {
		$level = $evolution->getLevel();
		$versionGroup = $this->versionGroupRepository->getById($evolution->getVersionGroupId());

		$weather = match ($versionGroup->getGenerationId()->value()) {
			6 => "rain",
			default => "rain or fog",
		};

		return new EvolutionTableMethod(
			"Level up, starting at level $level, during $weather in the overworld",
		);
	}

	/**
	 * For Rockruff (Own Tempo) into Lycanroc (Dusk Form).
	 */
	private function levelUpDusk(
		Evolution $evolution,
	) : EvolutionTableMethod {
		$level = $evolution->getLevel();
		$time = self::getEveningText($evolution->getVersionGroupId());

		return new EvolutionTableMethod(
			"Level up, $time, starting at level $level",
		);
	}

	/**
	 * For Galarian Yamask into Runerigus.
	 */
	private function hpLostInBattle(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$number = $evolution->getOtherParameter();

		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$hp = $statNames[StatId::HP]->getName();

		return new EvolutionTableMethod(
			"Pass under the rock arch in Dusty Bowl after taking at least $number $hp in damage from attacks without fainting",
		);
	}

	/**
	 * For Milcery into Alcremie.
	 */
	private function spin(
		Evolution $evolution,
		string $item,
	) : EvolutionTableMethod {
		$spinType = new AlcremieSpinType($evolution->getOtherParameter());

		$direction = $spinType->getDirection();
		$duration = $spinType->getDuration();
		$timeOfDay = $spinType->getTimeOfDay($evolution->getVersionGroupId());

		return new EvolutionTableMethod(
			"After spinning $direction for $duration $timeOfDay, while holding $item",
		);
	}

	/**
	 * For Toxel into Toxtricity.
	 */
	private function levelUpNatures(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$level = $evolution->getLevel();

		$natures = $this->dexNatureRepository->getByToxelEvo(
			$languageId,
			$evolution->getEvoIntoId(),
		);
		$natures[array_key_last($natures)] = 'or ' . $natures[array_key_last($natures)];
		$natures = implode(', ', $natures);

		return new EvolutionTableMethod(
			"Level up, starting at level $level, if its Nature is $natures",
		);
	}

	/**
	 * For Bisharp into Kingambit.
	 */
	private function levelUpDefeatEquals(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$number = $evolution->getOtherParameter();

		$bisharp = $this->textLinkRepository->getForPokemon(
			$evolution->getVersionGroupId(),
			$languageId,
			New PokemonId(PokemonId::BISHARP),
		);
		$leadersCrest = $this->textLinkRepository->getForItem(
			$evolution->getVersionGroupId(),
			$languageId,
			new ItemId(ItemId::LEADERS_CREST),
		);

		$bisharp = $bisharp->getLinkHtml();
		$leadersCrest = $leadersCrest->getLinkHtml();

		return new EvolutionTableMethod(
			"Level up, after defeating $number $bisharp that hold a $leadersCrest",
		);
	}

	/**
	 * For Basculin (White-Striped Form) into Basculegion.
	 */
	private function levelUpRecoilDamage(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$number = $evolution->getOtherParameter();

		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$hp = $statNames[StatId::HP]->getName();

		$gender = match ($evolution->getEvoMethodId()->value()) {
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_MALE => 'males',
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_FEMALE => 'females',
		};

		return new EvolutionTableMethod(
			"Level up, after losing at least $number $hp from recoil damage, $gender only",
		);
	}

	/**
	 * For Stantler into Wyrdeer.
	 */
	private function useMoveAgileStyle(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$psyshieldBash = $this->textLinkRepository->getForMove(
			$evolution->getVersionGroupId(),
			$languageId,
			new MoveId(MoveId::PSYSHIELD_BASH),
		);
		$psyshieldBash = $psyshieldBash->getLinkHtml();

		return new EvolutionTableMethod(
			"Use $psyshieldBash in the agile style 20 times",
		);
	}

	/**
	 * For Hisuian Qwilfish into Overqwil.
	 */
	private function useMoveStrongStyle(
		Evolution $evolution,
		LanguageId $languageId,
	) : EvolutionTableMethod {
		$barbBarrage = $this->textLinkRepository->getForMove(
			$evolution->getVersionGroupId(),
			$languageId,
			new MoveId(MoveId::BARB_BARRAGE),
		);
		$barbBarrage = $barbBarrage->getLinkHtml();

		return new EvolutionTableMethod(
			"Use $barbBarrage in the strong style 20 times",
		);
	}

	private function getFriendship(GenerationId $generationId) : int
	{
		return match ($generationId->value()) {
			2, 3, 4, 5, 6, 7, => 220,
			default => 160,
		};
	}

	public static function getEveningText(VersionGroupId $versionGroupId) : string
	{
		return match ($versionGroupId->value()) {
			VersionGroupId::ULTRA_SUN_ULTRA_MOON => 'between 5:00 and 5:59 PM',
			VersionGroupId::SWORD_SHIELD => 'between 7:00 and 7:59 PM',
			VersionGroupId::SCARLET_VIOLET => 'during the evening',
		};
	}
}
