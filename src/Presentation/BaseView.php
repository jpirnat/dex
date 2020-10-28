<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BaseModel;
use Jp\Dex\Domain\Languages\LanguageName;

final class BaseView
{
	public function __construct(
		private BaseModel $baseModel,
	) {}

	/**
	 * Get the variables needed for the base template.
	 *
	 * @return array
	 */
	public function getBaseVariables() : array
	{
		// Start the data array.
		$data = [
			'currentYear' => $this->baseModel->getCurrentYear(),
			'currentLanguageId' => $this->baseModel->getCurrentLanguageId()->value(),
		];

		// Get data for language names.
		$languageNames = $this->baseModel->getLanguageNames();
		uasort(
			$languageNames,
			function (LanguageName $a, LanguageName $b) : int {
				return $a->getName() <=> $b->getName();
			}
		);
		foreach ($languageNames as $languageName) {
			$data['languages'][] = [
				'id' => $languageName->getNamedLanguageId()->value(),
				'name' => $languageName->getName(),
			];
		}

		return $data;
	}
}
