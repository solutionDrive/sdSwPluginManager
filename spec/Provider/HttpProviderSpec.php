<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use sd\SwPluginManager\Provider\HttpProvider;
use sd\SwPluginManager\Provider\ProviderInterface;

class HttpProviderSpec extends ObjectBehavior
{
    public function let(Client $guzzleClient): void
    {
        $this->beConstructedWith($guzzleClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(HttpProvider::class);
    }

    public function it_is_a_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_can_load_simple(Client $guzzleClient, ResponseInterface $guzzleResponse): void
    {
        $url = 'https://sd.test/url/to/file.zip';

        $guzzleResponse->getStatusCode()->willReturn(200);
        $guzzleClient->get(
            Argument::exact($url),
            Argument::withKey('sink')
        )
        ->willReturn($guzzleResponse)
        ->shouldBeCalled();

        $this->loadFile(['src' => $url]);
    }

    public function it_can_load_with_auth(Client $guzzleClient, ResponseInterface $guzzleResponse): void
    {
        $url = 'https://sd.test/url/to/file.zip';

        $guzzleResponse->getStatusCode()->willReturn(200);
        $guzzleClient->get(
            Argument::exact($url),
            Argument::withEntry('auth', ['user', 'pass'])
        )
        ->willReturn($guzzleResponse)
        ->shouldBeCalled();

        $this->loadFile([
            'src' => $url,
            'username' => 'user',
            'password' => 'pass',
        ]);
    }

    public function it_cannot_load_with_empty_url(): void
    {
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [[]]);
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [['src' => '']]);
    }

    public function it_supports(): void
    {
        $this->supports('http')->shouldReturn(true);
        $this->supports('none')->shouldReturn(false);
        $this->supports('other')->shouldReturn(false);
        $this->supports('file')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
    }
}
