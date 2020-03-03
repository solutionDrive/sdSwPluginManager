<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Provider;

use GuzzleHttp\Client;

class HttpProvider implements ProviderInterface
{
    /** @var Client */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * {@inheritdoc}
     */
    public function loadFile(array $parameters, bool $force = false): ?string
    {
        if (true === empty($parameters['src'])) {
            throw new \RuntimeException('src must not be empty for HttpProvider.');
        }

        $auth = null;
        if (false === empty($parameters['username']) && false === empty($parameters['password'])) {
            $auth = [$parameters['username'], $parameters['password']];
        }

        $headers = [];
        if (false === empty($parameters['header'])) {
            $headers = $parameters['header'];
        }

        $tmpName = \tempnam('/tmp', 'sw-plugin-');
        $this->downloadFile($parameters['src'], $tmpName, $auth, $headers);

        return $tmpName;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $providerName): bool
    {
        return 'http' === $providerName;
    }

    /**
     * @param array|string[]|null $auth    See http://docs.guzzlephp.org/en/stable/request-options.html#auth .
     * @param array|string[]      $headers See http://docs.guzzlephp.org/en/stable/request-options.html#headers .
     */
    private function downloadFile(
        string $url,
        string $targetFilename,
        ?array $auth = null,
        array $headers = []
    ): void {
        $response = $this->guzzleClient->get(
            $url,
            [
                'allow_redirects' => true,
                'auth'            => $auth,
                'headers'         => $headers,
                'sink'            => $targetFilename,
            ]
        );

        $statusCode = $response->getStatusCode();
        if (300 <= $statusCode || 199 >= $statusCode) {
            throw new \RuntimeException('Could not download plugin.');
        }
    }
}
