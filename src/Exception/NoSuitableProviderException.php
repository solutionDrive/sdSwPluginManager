<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Exception;

use Throwable;

class NoSuitableProviderException extends \RuntimeException
{
    /** @var string */
    private $provider = '';

    public function __construct(
        string $provider,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->provider = $provider;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
