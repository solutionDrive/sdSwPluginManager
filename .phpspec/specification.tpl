<?php

namespace %namespace%;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use %subject%;

class %name% extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(%subject_class%::class);
    }

    public function it_implements_%subject_class%_interface()
    {
        $this->shouldImplement(%subject_class%Interface::class);
    }
}
