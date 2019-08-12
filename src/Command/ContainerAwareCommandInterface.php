<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

interface ContainerAwareCommandInterface
{
    public function setContainer(ContainerInterface $container): void;

    public function getContainer(): ContainerInterface;
}
