<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Exception;

use Throwable;

class ZipFileCouldNotBeExtractedException extends \RuntimeException
{
    /** @var string */
    private $zipPath = '';

    /**
     * @param string $zipPath path to the zip file that failed
     */
    public function __construct(string $zipPath, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->zipPath = $zipPath;
    }

    public function getZipPath(): string
    {
        return $this->zipPath;
    }
}
