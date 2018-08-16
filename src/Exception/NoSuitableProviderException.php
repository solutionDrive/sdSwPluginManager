<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Exception;

use Throwable;

class NoSuitableProviderException extends \RuntimeException
{
    /** @var string */
    private $provider = '';

    public function __construct($provider, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
