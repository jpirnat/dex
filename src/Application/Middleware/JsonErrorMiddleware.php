<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Middleware;

use Jp\Dex\Presentation\RendererInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;

final readonly class JsonErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $environment,
        private RendererInterface $renderer,
        private string $errorEmailAddress,
        private MailerInterface $mailer,
    ) {}

    /**
     * Intercept all errors and exceptions in the code and return an error json
     * response object.
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
/*
        if ($this->environment !== 'production') {
            // In development environments, we want to see the errors. They can
            // run, but they can't hide!
            $whoops = new Run();
            $whoops->prependHandler(new JsonResponseHandler());
            $whoops->register();

            return $handler->handle($request);
        }
*/

        // In production environments, the user should not see PHP errors.
        // Instead, redirect them to our error page.
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            // Email the developer.
            $body = $this->renderer->render('emails/site-error.twig', [
                'url' => $request->getUri()->getPath(),
                'method' => $request->getMethod(),
                'error' => (string) $e
            ]);
            $email = new Email()
                ->from(new Address($this->errorEmailAddress))
                ->to(new Address($this->errorEmailAddress))
                ->subject('!!! Porydex Error !!!')
                ->html($body)
            ;
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface) {}

            return new JsonResponse(['error' => true]);
        }
    }
}
