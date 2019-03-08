<?php

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
        $id = '',
        $name = '',
        $version = '',
        $author = '',
        $activated = false,
        $installed = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
        $this->author = $author;
        $this->activated = $activated;
        $this->installed = $installed;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return $this->activated;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }
}
