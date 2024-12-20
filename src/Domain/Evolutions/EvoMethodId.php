<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\EntityId;

final class EvoMethodId extends EntityId
{
	public const int LEVEL_UP_FRIENDSHIP = 1;
	public const int LEVEL_UP_FRIENDSHIP_MORNING = 2;
	public const int LEVEL_UP_FRIENDSHIP_NIGHT = 3;
	public const int LEVEL_UP = 4;
	public const int TRADE = 5;
	public const int TRADE_HELD_ITEM = 6;
	public const int TRADE_SHELMET_KARRABLAST = 7;
	public const int USE_ITEM = 8;
	public const int LEVEL_UP_ATK_GT_DEF = 9;
	public const int LEVEL_UP_ATK_EQ_DEF = 10;
	public const int LEVEL_UP_DEF_GT_ATK = 11;
	public const int LEVEL_UP_EC_LT_FIVE = 12;
	public const int LEVEL_UP_EC_GT_EQ_FIVE = 13;
	public const int LEVEL_UP_NINJASK = 14;
	public const int LEVEL_UP_SHEDINJA = 15;
	public const int LEVEL_UP_BEAUTY = 16;
	public const int USE_ITEM_MALE = 17;
	public const int USE_ITEM_FEMALE = 18;
	public const int LEVEL_UP_HELD_ITEM_DAY = 19;
	public const int LEVEL_UP_HELD_ITEM_NIGHT = 20;
	public const int LEVEL_UP_KNOW_MOVE = 21;
	public const int LEVEL_UP_WITH_TEAMMATE = 22;
	public const int LEVEL_UP_MALE = 23;
	public const int LEVEL_UP_FEMALE = 24;
	public const int LEVEL_UP_ELECTRIC = 25;
	public const int LEVEL_UP_FOREST = 26;
	public const int LEVEL_UP_COLD = 27;
	public const int LEVEL_UP_INVERTED = 28;
	public const int LEVEL_UP_AFFECTION_50_MOVE_TYPE = 29;
	public const int LEVEL_UP_MOVE_TYPE = 30;
	public const int LEVEL_UP_WEATHER = 31;
	public const int LEVEL_UP_MORNING = 32;
	public const int LEVEL_UP_NIGHT = 33;
	public const int LEVEL_UP_FORM_FEMALE_1 = 34;
	public const int LEVEL_UP_VERSION = 36;
	public const int LEVEL_UP_VERSION_DAY = 37;
	public const int LEVEL_UP_VERSION_NIGHT = 38;
	public const int LEVEL_UP_SUMMIT = 39;
	public const int LEVEL_UP_DUSK = 40;
	public const int LEVEL_UP_WORMHOLE = 41;
	public const int USE_ITEM_WORMHOLE = 42;
	public const int CRITICAL_HITS_IN_BATTLE = 43;
	public const int HP_LOST_IN_BATTLE = 44;
	public const int SPIN = 45;
	public const int LEVEL_UP_NATURE_AMPED = 46;
	public const int LEVEL_UP_NATURE_LOW_KEY = 47;
	public const int TOWER_OF_DARKNESS = 48;
	public const int TOWER_OF_WATERS = 49;
	public const int LEVEL_UP_WALK_STEPS_WITH = 50;
	public const int LEVEL_UP_UNION_CIRCLE = 51;
	public const int LEVEL_UP_IN_BATTLE_EC_25 = 52;
	public const int LEVEL_UP_IN_BATTLE_EC_ELSE = 53;
	public const int LEVEL_UP_COLLECT_999 = 54;
	public const int LEVEL_UP_DEFEAT_EQUALS = 55;
	public const int LEVEL_UP_USE_MOVE_SPECIAL = 56;
	public const int LEVEL_UP_KNOW_MOVE_EC_ELSE = 57;
	public const int LEVEL_UP_KNOW_MOVE_EC_25 = 58;
	public const int LEVEL_UP_RECOIL_DAMAGE_MALE = 59;
	public const int LEVEL_UP_RECOIL_DAMAGE_FEMALE = 60;
	// public const int HISUI = 61; // This evolution method exists in Scarlet/Violet, but does nothing.
	public const int USE_ITEM_FULL_MOON = 90;
	public const int USE_MOVE_AGILE_STYLE = 91;
	public const int USE_MOVE_STRONG_STYLE = 92;
	public const int USE_ITEM_DAY = 201;
	public const int USE_ITEM_NIGHT = 202;

	public function needsFriendship() : bool
	{
		return in_array($this->id, [
			self::LEVEL_UP_FRIENDSHIP,
			self::LEVEL_UP_FRIENDSHIP_MORNING,
			self::LEVEL_UP_FRIENDSHIP_NIGHT,
		]);
	}

	public function needsItem() : bool
	{
		return in_array($this->id, [
			self::TRADE_HELD_ITEM,
			self::USE_ITEM,
			self::USE_ITEM_MALE,
			self::USE_ITEM_FEMALE,
			self::LEVEL_UP_HELD_ITEM_DAY,
			self::LEVEL_UP_HELD_ITEM_NIGHT,
			self::USE_ITEM_WORMHOLE,
			self::SPIN, // This evolution method doesn't use an item parameter in the games. It does here, for simplicity.
			self::USE_ITEM_FULL_MOON,
			self::USE_ITEM_DAY,
			self::USE_ITEM_NIGHT,
		]);
	}

	public function needsMove() : bool
	{
		return in_array($this->id, [
			self::LEVEL_UP_KNOW_MOVE,
			self::LEVEL_UP_KNOW_MOVE_EC_ELSE,
			self::LEVEL_UP_KNOW_MOVE_EC_25,
		]);
	}

	public function needsPokemon() : bool
	{
		return in_array($this->id, [
			self::LEVEL_UP_WITH_TEAMMATE,
		]);
	}

	public function needsType() : bool
	{
		return in_array($this->id, [
			self::LEVEL_UP_AFFECTION_50_MOVE_TYPE,
			self::LEVEL_UP_MOVE_TYPE,
		]);
	}

	public function needsVersion() : bool
	{
		return in_array($this->id, [
			self::LEVEL_UP_VERSION,
			self::LEVEL_UP_VERSION_DAY,
			self::LEVEL_UP_VERSION_NIGHT,
		]);
	}
}
