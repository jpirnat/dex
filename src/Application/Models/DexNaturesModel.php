<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\NatureNameRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;

final class DexNaturesModel
{
	/** @var NatureRepositoryInterface $natureRepository */
	private $natureRepository;

	/** @var NatureNameRepositoryInterface $natureNameRepository */
	private $natureNameRepository;

	/** @var StatNameRepositoryInterface $statNameRepository */
	private $statNameRepository;


	/** @var string $generationIdentifier */
	private $generationIdentifier;

	/** @var array $natures */
	private $natures = [];


	/**
	 * Constructor.
	 *
	 * @param NatureRepositoryInterface $natureRepository
	 * @param NatureNameRepositoryInterface $natureNameRepository
	 * @param StatNameRepositoryInterface $statNameRepository
	 */
	public function __construct(
		NatureRepositoryInterface $natureRepository,
		NatureNameRepositoryInterface $natureNameRepository,
		StatNameRepositoryInterface $statNameRepository
	) {
		$this->natureRepository = $natureRepository;
		$this->natureNameRepository = $natureNameRepository;
		$this->statNameRepository = $statNameRepository;
	}

	/**
	 * Set data for the dex natures page.
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
		$this->generationIdentifier = $generationIdentifier;

		$natures = $this->natureRepository->getAll();
		$natureNames = $this->natureNameRepository->getByLanguage($languageId);
		$statNames = $this->statNameRepository->getByLanguage($languageId);

		$this->natures = [];

		foreach ($natures as $nature) {
			$natureId = $nature->getId()->value();
			$natureName = $natureNames[$natureId];

			$increasedStatName = '-';
			$decreasedStatName = '-';
			if ($nature->getIncreasedStatId() !== null) {
				$increasedStatId = $nature->getIncreasedStatId()->value();
				$increasedStatName = $statNames[$increasedStatId];
				$increasedStatName = $increasedStatName->getName();
			}
			if ($nature->getDecreasedStatId() !== null) {
				$decreasedStatId = $nature->getDecreasedStatId()->value();
				$decreasedStatName = $statNames[$decreasedStatId];
				$decreasedStatName = $decreasedStatName->getName();
			}

			$this->natures[] = [
				'name' => $natureName->getName(),
				'increasedStatName' => $increasedStatName,
				'decreasedStatName' => $decreasedStatName,
				'vcExpRemainder' => $nature->getVcExpRemainder(),
			];
		}
	}

	/**
	 * Get the generation identifier.
	 *
	 * @return string
	 */
	public function getGenerationIdentifier() : string
	{
		return $this->generationIdentifier;
	}

	/**
	 * Get the natures.
	 *
	 * @return array
	 */
	public function getNatures() : array
	{
		return $this->natures;
	}
}
