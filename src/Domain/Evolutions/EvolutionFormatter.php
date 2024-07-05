<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\Conditions\ConditionId;
use Jp\Dex\Domain\Conditions\ConditionNameRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Natures\DexNatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionNameRepositoryInterface;

final readonly class EvolutionFormatter
{
	public function __construct(
		private VersionGroupRepositoryInterface $versionGroupRepository,
		private ItemNameRepositoryInterface $itemNameRepository,
		private MoveNameRepositoryInterface $moveNameRepository,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private StatNameRepositoryInterface $statNameRepository,
		private DexTypeRepositoryInterface $dexTypeRepository,
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
	) : string {
		$evoMethodId = $evolution->getEvoMethodId();

		$level = $evolution->getLevel();

		$friendship = 0;
		if ($evoMethodId->needsFriendship()) {
			$versionGroup = $this->versionGroupRepository->getById($evolution->getVersionGroupId());
			$friendship = $this->getFriendship($versionGroup->getGenerationId());
		}

		$item = '';
		if ($evoMethodId->needsItem()) {
			$item = $this->itemNameRepository->getByLanguageAndItem(
				$languageId,
				$evolution->getItemId(),
			);
			$item = $item->getName();
		}

		$move = '';
		if ($evoMethodId->needsMove()) {
			$move = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$evolution->getMoveId(),
			);
			$move = $move->getName();
		}

		$pokemon = '';
		if ($evoMethodId->needsPokemon()) {
			$pokemon = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$evolution->getPokemonId(),
			);
			$pokemon = $pokemon->getName();
		}

		$type = '';
		if ($evoMethodId->needsType()) {
			$type = $this->dexTypeRepository->getById(
				$evolution->getTypeId(),
				$languageId,
			);
			$type = $type->getName();
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

		$text = match ($evoMethodId->value()) {
			EvoMethodId::LEVEL_UP_FRIENDSHIP => "Level up, with at least $friendship friendship",
			EvoMethodId::LEVEL_UP_FRIENDSHIP_MORNING => "Level up, during the day, with at least $friendship friendship",
			EvoMethodId::LEVEL_UP_FRIENDSHIP_NIGHT => "Level up, during the night, with at least $friendship friendship",
			EvoMethodId::LEVEL_UP => "Level up, starting at level $level",
			EvoMethodId::TRADE => "Trade",
			EvoMethodId::TRADE_HELD_ITEM => "Trade, while holding $item",
			EvoMethodId::TRADE_SHELMET_KARRABLAST => $this->tradeShelmetKarrablast($evolution, $languageId),
			EvoMethodId::USE_ITEM => "Use $item",
			EvoMethodId::LEVEL_UP_ATK_GT_DEF => $this->tyrogue($evolution, $languageId),
			EvoMethodId::LEVEL_UP_ATK_EQ_DEF => $this->tyrogue($evolution, $languageId),
			EvoMethodId::LEVEL_UP_DEF_GT_ATK => $this->tyrogue($evolution, $languageId),
			EvoMethodId::LEVEL_UP_EC_LT_FIVE => "Level up, starting at level $level, 50% chance",
			EvoMethodId::LEVEL_UP_EC_GT_EQ_FIVE => "Level up, starting at level $level, 50% chance",
			EvoMethodId::LEVEL_UP_NINJASK => "Level up, starting at level $level",
			EvoMethodId::LEVEL_UP_SHEDINJA => $this->levelUpShedinja($evolution, $languageId),
			EvoMethodId::LEVEL_UP_BEAUTY => $this->levelUpBeauty($evolution, $languageId),
			EvoMethodId::USE_ITEM_MALE => "Use $item, males only",
			EvoMethodId::USE_ITEM_FEMALE => "Use $item, female only",
			EvoMethodId::LEVEL_UP_HELD_ITEM_DAY => "Level up, during the day, while holding $item",
			EvoMethodId::LEVEL_UP_HELD_ITEM_NIGHT => "Level up, during the night, while holding $item",
			EvoMethodId::LEVEL_UP_KNOW_MOVE => "Level up, knowing $move",
			EvoMethodId::LEVEL_UP_WITH_TEAMMATE => "Level up, with $pokemon in the party",
			EvoMethodId::LEVEL_UP_MALE => "Level up, males only, starting at level $level",
			EvoMethodId::LEVEL_UP_FEMALE => "Level up, females only, starting at level $level",
			EvoMethodId::LEVEL_UP_ELECTRIC => "Level up, around a special magnetic field",
			EvoMethodId::LEVEL_UP_FOREST => "Level up near a Moss Rock",
			EvoMethodId::LEVEL_UP_COLD => "Level up near an Ice Rock",
			EvoMethodId::LEVEL_UP_INVERTED => "Level up, starting at $level, while the game system is held upside-down",
			EvoMethodId::LEVEL_UP_AFFECTION_50_MOVE_TYPE => $this->levelUpAffection50MoveType($evolution, $type),
			EvoMethodId::LEVEL_UP_MOVE_TYPE => "Level up, with a $type-type Pokémon in the party",
			EvoMethodId::LEVEL_UP_WEATHER => $this->levelUpWeather($evolution),
			EvoMethodId::LEVEL_UP_MORNING => "Level up, during the day, starting at level $level",
			EvoMethodId::LEVEL_UP_NIGHT => "Level up, during the night, starting at level $level",
			EvoMethodId::LEVEL_UP_FORM_FEMALE_1 => "Level up, females only, starting at level $level",
			EvoMethodId::LEVEL_UP_VERSION => "Level up, starting at level $level, in $version only",
			EvoMethodId::LEVEL_UP_VERSION_DAY => "Level up, during the day, starting at level $level, in $version only",
			EvoMethodId::LEVEL_UP_VERSION_NIGHT => "Level up, during the night, starting at level $level, in $version only",
			EvoMethodId::LEVEL_UP_SUMMIT => "Level up at Mount Lanakila",
			EvoMethodId::LEVEL_UP_DUSK => $this->levelUpDusk($evolution),
			EvoMethodId::LEVEL_UP_WORMHOLE => "Level up, starting at level $level, while in an Ultra Wormhole",
			EvoMethodId::USE_ITEM_WORMHOLE => "Use $item, while in an Ultra Wormhole",
			EvoMethodId::CRITICAL_HITS_IN_BATTLE => "Land $otherParameter critical hits in one battle",
			EvoMethodId::HP_LOST_IN_BATTLE => $this->hpLostInBattle($evolution, $languageId),
			EvoMethodId::SPIN => $this->spin($evolution, $item),
			EvoMethodId::LEVEL_UP_NATURE_AMPED => $this->levelUpNatures($evolution, $languageId),
			EvoMethodId::LEVEL_UP_NATURE_LOW_KEY => $this->levelUpNatures($evolution, $languageId),
			EvoMethodId::TOWER_OF_DARKNESS => "Read the Scroll of Darkness in the Tower of Darkness",
			EvoMethodId::TOWER_OF_WATERS => "Read the Scroll of Waters in the Tower of Waters",
			EvoMethodId::LEVEL_UP_WALK_STEPS_WITH => "Level up, while outside of its Poké Ball after walking $otherParameter steps using the Let's Go! feature",
			EvoMethodId::LEVEL_UP_UNION_CIRCLE => "Level up, starting at level $level, while in a Union Circle group",
			EvoMethodId::LEVEL_UP_IN_BATTLE_EC_25 => "Level up, starting at level $level, 1% chance",
			EvoMethodId::LEVEL_UP_IN_BATTLE_EC_ELSE => "Level up, starting at level $level, 99% chance",
			EvoMethodId::LEVEL_UP_COLLECT_999 => "Level up, with $otherParameter Gimmighoul Coins in your bag",
			EvoMethodId::LEVEL_UP_DEFEAT_EQUALS => $this->levelUpDefeatEquals($evolution, $languageId),
			EvoMethodId::LEVEL_UP_USE_MOVE_SPECIAL => "Level up, after using $move $otherParameter times",
			EvoMethodId::LEVEL_UP_KNOW_MOVE_EC_ELSE => "Level up, knowing $move, 99% chance",
			EvoMethodId::LEVEL_UP_KNOW_MOVE_EC_25 => "Level up, knowing $move, 1% chance",
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_MALE => $this->levelUpRecoilDamage($evolution, $languageId),
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_FEMALE => $this->levelUpRecoilDamage($evolution, $languageId),
			EvoMethodId::USE_ITEM_FULL_MOON => "Use $item during a full moon",
			EvoMethodId::USE_MOVE_AGILE_STYLE => $this->useMoveAgileStyle($languageId),
			EvoMethodId::USE_MOVE_STRONG_STYLE => $this->useMoveStrongStyle($languageId),
			EvoMethodId::USE_ITEM_DAY => "Use $item during the day",
			EvoMethodId::USE_ITEM_NIGHT => "Use $item during the night",
		};

		return $text;
	}

	private function tradeShelmetKarrablast(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$inExchangeFor = match ($evolution->getEvoFromId()->value()) {
			FormId::KARRABLAST => FormId::SHELMET,
			FormId::SHELMET => FormId::KARRABLAST,
		};

		$pokemon = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			new PokemonId($inExchangeFor),
		);
		$pokemon = $pokemon->getName();

		return "Trade, in exchange for $pokemon";
	}

	/**
	 * For Tyrogue into Hitmonlee, Hitmonchan, or Hitmontop.
	 */
	private function tyrogue(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$level = $evolution->getLevel();

		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$attack = $statNames[StatId::ATTACK]->getName();
		$defense = $statNames[StatId::DEFENSE]->getName();

		$text = match ($evolution->getEvoMethodId()->value()) {
			EvoMethodId::LEVEL_UP_ATK_GT_DEF => "Level up, starting at level $level, when $attack > $defense",
			EvoMethodId::LEVEL_UP_ATK_EQ_DEF => "Level up, starting at level $level, when $attack = $defense",
			EvoMethodId::LEVEL_UP_DEF_GT_ATK => "Level up, starting at level $level, when $attack < $defense",
		};

		return $text;
	}

	/**
	 * For Nincada into Ninjask.
	 */
	private function levelUpShedinja(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$nincada = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			new PokemonId($evolution->getEvoFromId()->value()),
		);
		$ninjask = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			New PokemonId(PokemonId::NINJASK),
		);
		$pokeBall = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			new ItemId(ItemId::POKE_BALL),
		);

		$nincada = $nincada->getName();
		$ninjask = $ninjask->getName();
		$pokeBall = $pokeBall->getName();

		$text = "Evolve $nincada into $ninjask, with an empty party slot and a $pokeBall in your bag";

		return $text;
	}

	/**
	 * Mainly for Feebas into Milotic.
	 */
	private function levelUpBeauty(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$level = $evolution->getLevel();

		$number = $evolution->getOtherParameter();

		$beauty = $this->conditionNameRepository->getByLanguageAndCondition(
			$languageId,
			new ConditionId(ConditionId::BEAUTY),
		);
		$beauty = $beauty->getName();

		$text = "Level up, starting at level $level, with at least $number $beauty";

		return $text;
	}

	/**
	 * For Eevee into Sylveon.
	 */
	private function levelUpAffection50MoveType(
		Evolution $evolution,
		string $type,
	) : string {
		$versionGroup = $this->versionGroupRepository->getById($evolution->getVersionGroupId());

		$friendship = $this->getFriendship($versionGroup->getGenerationId());
		$friendshipOrAffection = match ($versionGroup->getGenerationId()->value()) {
			6, 7 => "at least 2 affection",
			default => "at least $friendship friendship",
		};

		$text = "Level up, with $friendshipOrAffection, while knowing a $type-type move";

		return $text;
	}

	/**
	 * For Sliggoo into Goodra.
	 */
	private function levelUpWeather(
		Evolution $evolution,
	) : string {
		$level = $evolution->getLevel();
		$versionGroup = $this->versionGroupRepository->getById($evolution->getVersionGroupId());

		$weather = match ($versionGroup->getGenerationId()->value()) {
			6 => "rain",
			default => "rain or fog",
		};

		$text = "Level up, starting at level $level, during $weather in the overworld";

		return $text;
	}

	/**
	 * For Rockruff (Own Tempo) into Lycanroc (Dusk Form).
	 */
	private function levelUpDusk(
		Evolution $evolution,
	) : string {
		$level = $evolution->getLevel();
		$time = self::getEveningText($evolution->getVersionGroupId());

		$text = "Level up, $time, starting at level $level";

		return $text;
	}

	/**
	 * For Galarian Yamask into Runerigus.
	 */
	private function hpLostInBattle(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$number = $evolution->getOtherParameter();

		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$hp = $statNames[StatId::HP]->getName();

		$text = "Pass under the rock arch in Dusty Bowl after taking at least $number $hp in damage from attacks without fainting";

		return $text;
	}

	/**
	 * For Milcery into Alcremie.
	 */
	private function spin(
		Evolution $evolution,
		string $item,
	) : string {
		$spinType = new AlcremieSpinType($evolution->getOtherParameter());

		$direction = $spinType->getDirection();
		$duration = $spinType->getDuration();
		$timeOfDay = $spinType->getTimeOfDay($evolution->getVersionGroupId());

		$text = "After spinning $direction for $duration $timeOfDay, while holding $item";

		return $text;
	}

	/**
	 * For Toxel into Toxtricity.
	 */
	private function levelUpNatures(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$level = $evolution->getLevel();

		$natures = $this->dexNatureRepository->getByToxelEvo(
			$languageId,
			$evolution->getEvoIntoId(),
		);
		$natures[array_key_last($natures)] = 'or ' . $natures[array_key_last($natures)];
		$natures = implode(', ', $natures);

		$text = "Level up, starting at level $level, if its Nature is $natures";

		return $text;
	}

	/**
	 * For Bisharp into Kingambit.
	 */
	private function levelUpDefeatEquals(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$number = $evolution->getOtherParameter();

		$bisharp = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			New PokemonId(PokemonId::BISHARP),
		);
		$leadersCrest = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			new ItemId(ItemId::LEADERS_CREST),
		);

		$bisharp = $bisharp->getName();
		$leadersCrest = $leadersCrest->getName();

		$text = "Level up, after defeating $number $bisharp that hold a $leadersCrest";

		return $text;
	}

	/**
	 * For Basculin (White-Striped Form) into Basculegion.
	 */
	private function levelUpRecoilDamage(
		Evolution $evolution,
		LanguageId $languageId,
	) : string {
		$number = $evolution->getOtherParameter();

		$statNames = $this->statNameRepository->getByLanguage($languageId);
		$hp = $statNames[StatId::HP]->getName();

		$text = match ($evolution->getEvoMethodId()->value()) {
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_MALE => "Level up, after losing at least $number $hp from recoil damage, males only",
			EvoMethodId::LEVEL_UP_RECOIL_DAMAGE_FEMALE => "Level up, after losing at least $number $hp from recoil damage, females only",
		};

		return $text;
	}

	/**
	 * For Stantler into Wyrdeer.
	 */
	private function useMoveAgileStyle(
		LanguageId $languageId,
	) : string {
		$psyshieldBash = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			new MoveId(MoveId::PSYSHIELD_BASH),
		);
		$psyshieldBash = $psyshieldBash->getName();

		$text = "Use $psyshieldBash in the agile style 20 times";

		return $text;
	}

	/**
	 * For Hisuian Qwilfish into Overqwil.
	 */
	private function useMoveStrongStyle(
		LanguageId $languageId,
	) : string {
		$barbBarrage = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			new MoveId(MoveId::BARB_BARRAGE),
		);
		$barbBarrage = $barbBarrage->getName();

		$text = "Use $barbBarrage in the strong style 20 times";

		return $text;
	}

	public function getFriendship(GenerationId $generationId) : int
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
