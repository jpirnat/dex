<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatName;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

class StatNameModel
{
	public function __construct(
		private StatNameRepositoryInterface $statNameRepository,
	) {}

	/**
	 * Get the names of the stats for this version group.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$statNames = $this->statNameRepository->getByLanguage($languageId);

		if ($versionGroupId->value() <= VersionGroupId::YELLOW) {
			// Not every language has a canon name for gen 1's Special stat.
			$special = new StatName(
				new LanguageId(LanguageId::ENGLISH),
				new StatId(StatId::SPECIAL),
				'Special',
				'Spc'
			);

			return [[
				'key' => 'hp',
				'abbr' => $statNames[StatId::HP]->getAbbreviation(),
				'name' => $statNames[StatId::HP]->getName(),
			], [
				'key' => 'atk',
				'abbr' => $statNames[StatId::ATTACK]->getAbbreviation(),
				'name' => $statNames[StatId::ATTACK]->getName(),
			], [
				'key' => 'def',
				'abbr' => $statNames[StatId::DEFENSE]->getAbbreviation(),
				'name' => $statNames[StatId::DEFENSE]->getName(),
			], [
				'key' => 'spc',
				'abbr' => ($statNames[StatId::SPECIAL] ?? $special)->getAbbreviation(),
				'name' => ($statNames[StatId::SPECIAL] ?? $special)->getName(),
			], [
				'key' => 'spe',
				'abbr' => $statNames[StatId::SPEED]->getAbbreviation(),
				'name' => $statNames[StatId::SPEED]->getName(),
			]];
		}

		return [[
			'key' => 'hp',
			'abbr' => $statNames[StatId::HP]->getAbbreviation(),
			'name' => $statNames[StatId::HP]->getName(),
		], [
			'key' => 'atk',
			'abbr' => $statNames[StatId::ATTACK]->getAbbreviation(),
			'name' => $statNames[StatId::ATTACK]->getName(),
		], [
			'key' => 'def',
			'abbr' => $statNames[StatId::DEFENSE]->getAbbreviation(),
			'name' => $statNames[StatId::DEFENSE]->getName(),
		], [
			'key' => 'spa',
			'abbr' => $statNames[StatId::SPECIAL_ATTACK]->getAbbreviation(),
			'name' => $statNames[StatId::SPECIAL_ATTACK]->getName(),
		], [
			'key' => 'spd',
			'abbr' => $statNames[StatId::SPECIAL_DEFENSE]->getAbbreviation(),
			'name' => $statNames[StatId::SPECIAL_DEFENSE]->getName(),
		], [
			'key' => 'spe',
			'abbr' => $statNames[StatId::SPEED]->getAbbreviation(),
			'name' => $statNames[StatId::SPEED]->getName(),
		]];
	}

	/**
	 * Get the names of the stats for this generation.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId,
	) : array {
		$statNames = $this->statNameRepository->getByLanguage($languageId);

		if ($generationId->value() === 1) {
			// Not every language has a canon name for gen 1's Special stat.
			$special = new StatName(
				new LanguageId(LanguageId::ENGLISH),
				new StatId(StatId::SPECIAL),
				'Special',
				'Spc'
			);

			return [[
				'key' => 'hp',
				'abbr' => $statNames[StatId::HP]->getAbbreviation(),
				'name' => $statNames[StatId::HP]->getName(),
			], [
				'key' => 'atk',
				'abbr' => $statNames[StatId::ATTACK]->getAbbreviation(),
				'name' => $statNames[StatId::ATTACK]->getName(),
			], [
				'key' => 'def',
				'abbr' => $statNames[StatId::DEFENSE]->getAbbreviation(),
				'name' => $statNames[StatId::DEFENSE]->getName(),
			], [
				'key' => 'spc',
				'abbr' => ($statNames[StatId::SPECIAL] ?? $special)->getAbbreviation(),
				'name' => ($statNames[StatId::SPECIAL] ?? $special)->getName(),
			], [
				'key' => 'spe',
				'abbr' => $statNames[StatId::SPEED]->getAbbreviation(),
				'name' => $statNames[StatId::SPEED]->getName(),
			]];
		}

		return [[
			'key' => 'hp',
			'abbr' => $statNames[StatId::HP]->getAbbreviation(),
			'name' => $statNames[StatId::HP]->getName(),
		], [
			'key' => 'atk',
			'abbr' => $statNames[StatId::ATTACK]->getAbbreviation(),
			'name' => $statNames[StatId::ATTACK]->getName(),
		], [
			'key' => 'def',
			'abbr' => $statNames[StatId::DEFENSE]->getAbbreviation(),
			'name' => $statNames[StatId::DEFENSE]->getName(),
		], [
			'key' => 'spa',
			'abbr' => $statNames[StatId::SPECIAL_ATTACK]->getAbbreviation(),
			'name' => $statNames[StatId::SPECIAL_ATTACK]->getName(),
		], [
			'key' => 'spd',
			'abbr' => $statNames[StatId::SPECIAL_DEFENSE]->getAbbreviation(),
			'name' => $statNames[StatId::SPECIAL_DEFENSE]->getName(),
		], [
			'key' => 'spe',
			'abbr' => $statNames[StatId::SPEED]->getAbbreviation(),
			'name' => $statNames[StatId::SPEED]->getName(),
		]];
	}
}
