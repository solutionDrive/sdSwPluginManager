<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Service;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;
use sd\SwPluginManager\Service\StreamTranslator;
use sd\SwPluginManager\Service\StreamTranslatorInterface;

class StreamTranslatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(StreamTranslator::class);
    }

    public function it_implements_ResponseJsonTranslator_interface()
    {
        $this->shouldImplement(StreamTranslatorInterface::class);
    }

    public function it_can_translate_a_stream_to_array(
        StreamInterface $stream
    ) {
        $data = json_encode([
            'token'     => 'abc',
            'locale'    => 'de_DE',
        ]);
        $stream->__toString()
            ->willReturn($data);

        $this->translateToArray($stream)
            ->shouldBeArray();
    }
}
