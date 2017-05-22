<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\LanguageModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class LanguageController
{
	/** @var LanguageModel $languageModel */
	private $languageModel;

	/**
	 * Constructor.
	 *
	 * @param LanguageModel $languageModel
	 */
	public function __construct(
		LanguageModel $languageModel
	) {
		$this->languageModel = $languageModel;
	}

	/**
	 * Set the user's language.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setLanguage(ServerRequestInterface $request) : void
	{
		$languageId = new LanguageId((int) $request->getQueryParams()['id'] ?? 0);

		$this->languageModel->setLanguage($languageId);
	}
}
