<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

interface ContainerAwareCommandInterface
{
    public function setContainer(ContainerInterface $container): void;

    public function getContainer(): ContainerInterface;
}
