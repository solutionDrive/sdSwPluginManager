<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Entity;

class DeployedPluginState
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /** @var string */
    private $author;

    /** @var bool */
    private $activated;

    /** @var bool */
    private $installed;

    public function __construct(
        string $id = '',
        string $name = '',
        string $version = '',
        string $author = '',
        bool $activated = false,
        bool $installed = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
        $this->author = $author;
        $this->activated = $activated;
        $this->installed = $installed;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function isInstalled(): bool
    {
        return $this->installed;
    }
}
