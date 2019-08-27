<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Provider\FilesystemProvider;
use sd\SwPluginManager\Provider\ProviderInterface;

class FilesystemProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FilesystemProvider::class);
    }

    public function it_is_a_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_can_load_file(): void
    {
        $this->loadFile(['src' => './test/file/path.zip'])->shouldReturn('./test/file/path.zip');
    }

    public function it_can_force_load_file(): void
    {
        $this->loadFile(['src' => './test/file/path.zip'], true)->shouldReturn('./test/file/path.zip');
    }

    public function it_cannot_load_with_empty_filename(): void
    {
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [[]]);
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [['src' => '']]);
    }

    public function it_supports(): void
    {
        $this->supports('filesystem')->shouldReturn(true);
        $this->supports('none')->shouldReturn(false);
        $this->supports('other')->shouldReturn(false);
        $this->supports('http')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
    }
}
