<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

/**
 * This should only be used by the dependency injection container.
 */
final readonly class MailerStaticFactory
{
    public static function createMailer(
        string $host,
        string $port,
        string $user,
        string $pass,
    ): MailerInterface {
        return new Mailer(Transport::fromDsn("smtp://$user:$pass@$host:$port"));
    }
}
