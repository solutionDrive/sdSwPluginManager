<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
    public function loadFile($parameters)
    {
        if (true === empty($parameters['src'])) {
            throw new \RuntimeException('src must not be empty for FilesystemProvider.');
        }

        $auth = null;
        if (false === empty($parameters['username']) && false === empty($parameters['password'])) {
            $auth = [$parameters['username'], $parameters['password']];
        }

        $headers = [];
        if (false === empty($parameters['header'])) {
            $headers = $parameters['header'];
        }

        $tmpName = tempnam('/tmp', 'sw-plugin-');
        $this->downloadFile($parameters['src'], $tmpName, $auth, $headers);

        return $tmpName;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($providerName)
    {
        return 'http' === $providerName;
    }

    /**
     * @param string     $url
     * @param string     $targetFilename
     * @param array|null $auth           See http://docs.guzzlephp.org/en/stable/request-options.html#auth .
     * @param array      $headers        See http://docs.guzzlephp.org/en/stable/request-options.html#headers .
     */
    private function downloadFile($url, $targetFilename, $auth = null, $headers = [])
    {
        $response = $this->guzzleClient->get(
            $url,
            [
                'allow_redirects' => true,
                'auth'            => $auth,
                'headers'         => $headers,
                'sink'            => $targetFilename,
            ]
        );

        if (300 <= $response->getStatusCode() || 199 >= $response->getStatusCode()) {
            throw new \RuntimeException('Could not download plugin.');
        }
    }
}
