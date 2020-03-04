<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Provider;

interface ProviderInterface
{
    /**
     * @param array|string[] $parameters Parameters (including source, auth data, etc.)
     *
     * @return string|null Path to the downloaded ZIP file
     */
    public function loadFile(array $parameters, bool $force = false): ?string;

    public function supports(string $providerName): bool;
}
