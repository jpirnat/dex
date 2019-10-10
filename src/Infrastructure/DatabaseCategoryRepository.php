<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\Category;
use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Categories\CategoryRepositoryInterface;
use PDO;

final class DatabaseCategoryRepository implements CategoryRepositoryInterface
{
	/** @var PDO $db */
	private $db;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Get all categories.
	 *
	 * @return Category[] Indexed by id.
	 */
	public function getAll() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`icon`
			FROM `categories`'
		);
		$stmt->execute();

		$categories = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$category = new Category(
				new CategoryId($result['id']),
				$result['identifier'],
				$result['icon']
			);

			$categories[$result['id']] = $category;
		}

		return $categories;
	}
}
