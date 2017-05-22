<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Jp\Dex\Application\Models\LanguageModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class LanguageView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var LanguageModel $languageModel */
	private $languageModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param LanguageModel $languageModel
	 */
	public function __construct(
		Twig_Environment $twig,
		LanguageModel $languageModel
	) {
		$this->twig = $twig;
		$this->languageModel = $languageModel;
	}

	/**
	 * Set the user's language.
	 *
	 * @return ResponseInterface
	 */
	public function setLanguage() : ResponseInterface
	{
		$response = new Response();

		$languageId = $this->languageModel->getLanguageId();

		if ($languageId !== null) {
			/** @var SetCookie $setCookie */
			$setCookie = SetCookie::create('languageId');
			$setCookie = $setCookie->withValue($languageId->value());
			$setCookie = $setCookie->withExpires(new DateTime('+5 years'));
			$setCookie = $setCookie->withPath('/');
			$response = FigResponseCookies::set($response, $setCookie);
		}

		return $response;
	}
}
