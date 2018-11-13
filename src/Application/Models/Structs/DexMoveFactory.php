<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

use Jp\Dex\Domain\Categories\CategoryRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

class DexMoveFactory
{
	/** @var GenerationMoveRepositoryInterface $generationMoveRepository */
	private $generationMoveRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var DexTypeFactory $dexTypeFactory */
	private $dexTypeFactory;

	/** @var CategoryRepositoryInterface $categoryRepository */
	private $categoryRepository;

	/** @var MoveDescriptionRepositoryInterface $moveDescriptionRepository */
	private $moveDescriptionRepository;

	/**
	 * Constructor.
	 *
	 * @param GenerationMoveRepositoryInterface $generationMoveRepository
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param DexTypeFactory $dexTypeFactory
	 * @param CategoryRepositoryInterface $categoryRepository
	 * @param MoveDescriptionRepositoryInterface $moveDescriptionRepository
	 */
	public function __construct(
		GenerationMoveRepositoryInterface $generationMoveRepository,
		MoveRepositoryInterface $moveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		DexTypeFactory $dexTypeFactory,
		CategoryRepositoryInterface $categoryRepository,
		MoveDescriptionRepositoryInterface $moveDescriptionRepository
	) {
		$this->generationMoveRepository = $generationMoveRepository;
		$this->moveRepository = $moveRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->dexTypeFactory = $dexTypeFactory;
		$this->categoryRepository = $categoryRepository;
		$this->moveDescriptionRepository = $moveDescriptionRepository;
	}

	/**
	 * Get the dex moves for this generation and type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexMove[]
	 */
	public function getByGenerationAndType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array {
		$generationMoves = $this->generationMoveRepository->getByGenerationAndType(
			$generationId,
			$typeId
		);

		$type = $this->dexTypeFactory->getDexType($generationId, $typeId, $languageId);

		$categories = $this->categoryRepository->getAll();

		$moves = [];

		foreach ($generationMoves as $generationMove) {
			$move = $this->moveRepository->getById($generationMove->getMoveId());

			$moveName = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$move->getId()
			);

			$category = $categories[$generationMove->getCategoryId()->value()];

			$moveDescription = $this->moveDescriptionRepository->getByGenerationAndLanguageAndMove(
				$generationId,
				$languageId,
				$move->getId()
			);

			$moves[] = new DexMove(
				$move->getIdentifier(),
				$moveName->getName(),
				$type,
				$category->getIcon(),
				$generationMove->getPP(),
				$generationMove->getPower(),
				$generationMove->getAccuracy(),
				$generationMove->getPriority(),
				$moveDescription->getDescription()
			);
		}

		return $moves;
	}
}
