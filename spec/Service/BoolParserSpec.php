<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Service;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Service\BoolParser;
use sd\SwPluginManager\Service\BoolParserInterface;

class BoolParserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(BoolParser::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(BoolParserInterface::class);
    }

    public function it_can_detect_some_true()
    {
        $this->parse('true')->shouldReturn(true);
        $this->parse('True')->shouldReturn(true);
        $this->parse('TrUe')->shouldReturn(true);
        $this->parse('TRUE')->shouldReturn(true);
        $this->parse('yes')->shouldReturn(true);
        $this->parse('Yes')->shouldReturn(true);
        $this->parse('yEs')->shouldReturn(true);
        $this->parse('YES')->shouldReturn(true);
        $this->parse('on')->shouldReturn(true);
        $this->parse('On')->shouldReturn(true);
        $this->parse('oN')->shouldReturn(true);
        $this->parse('ON')->shouldReturn(true);
        $this->parse('1')->shouldReturn(true);
    }

    public function it_can_detect_some_false()
    {
        $this->parse('false')->shouldReturn(false);
        $this->parse('False')->shouldReturn(false);
        $this->parse('FaLSe')->shouldReturn(false);
        $this->parse('FALSE')->shouldReturn(false);
        $this->parse('no')->shouldReturn(false);
        $this->parse('No')->shouldReturn(false);
        $this->parse('nO')->shouldReturn(false);
        $this->parse('NO')->shouldReturn(false);
        $this->parse('off')->shouldReturn(false);
        $this->parse('Off')->shouldReturn(false);
        $this->parse('oFf')->shouldReturn(false);
        $this->parse('OFF')->shouldReturn(false);
        $this->parse('0')->shouldReturn(false);
    }
}
