<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Service\PluginVersionService;
use sd\SwPluginManager\Service\PluginVersionServiceInterface;
use sd\SwPluginManager\Service\TableParser;

class PluginVersionServiceSpec extends ObjectBehavior
{
    public function let(TableParser $tableParser): void
    {
        $this->beConstructedWith($tableParser);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PluginVersionService::class);
    }

    public function it_implements_the_correct_interface(): void
    {
        $this->shouldImplement(PluginVersionServiceInterface::class);
    }

    public function it_correctly_determines_if_a_plugin_needs_to_be_updated(
        TableParser $tableParser,
        ConfiguredPluginState $asdfPlugin,
        ConfiguredPluginState $nlxTest,
        ConfiguredPluginState $downgraded,
        ConfiguredPluginState $nonExisting
    ): void {
        $this->initializePluginVersions($tableParser);

        $asdfPlugin->getId()
            ->willReturn('asdfPlugin');

        $asdfPlugin->getVersion()
            ->willReturn('3.2.1');

        $nlxTest->getId()
            ->willReturn('nlxTest');

        $nlxTest->getVersion()
            ->willReturn('5.0.0');

        $downgraded->getId()
            ->willReturn('downgraded');

        $downgraded->getVersion()
            ->willReturn('1.9.2');

        $downgraded->getVersion()
            ->willReturn('dummy');

        $nonExisting->getId()
            ->willReturn('nonExisting');

        $nonExisting->getVersion()
            ->willReturn(null);

        $nonExisting->getProviderParameters()
            ->willReturn([]);

        $this->pluginNeedsUpdate($asdfPlugin)
            ->shouldReturn(true);

        $this->pluginNeedsUpdate($nlxTest)
            ->shouldReturn(false);

        $this->pluginNeedsUpdate($downgraded)
            ->shouldReturn(false);

        $this->pluginNeedsUpdate($nonExisting)
            ->shouldReturn(false);
    }

    public function it_should_fallback_on_provider_parameter_version_if_no_version_is_set(
        TableParser $tableParser,
        ConfiguredPluginState $plugin
    ): void {
        $this->initializePluginVersions($tableParser);

        $plugin->getId()
            ->willReturn('asdfPlugin');

        $plugin->getVersion()
            ->willReturn(null);

        $plugin->getProviderParameters()
            ->willReturn(['version' => '2.0.0']);

        $this->pluginNeedsUpdate($plugin)
            ->shouldReturn(true);
    }

    public function it_should_not_lookup_version_if_the_plugin_has_neither_version_nor_provider_parameter_version_set(
        ConfiguredPluginState $plugin
    ): void {
        $plugin->getId()
            ->willReturn('asdfPlugin');

        $plugin->getVersion()
            ->willReturn(null);

        $plugin->getProviderParameters()
            ->willReturn([]);

        $this->pluginNeedsUpdate($plugin)
            ->shouldReturn(false);
    }

    public function it_can_parse_the_plugin_list(): void
    {
        $tableParser = new TableParser(
            '|',
            "\n",
            6,
            true,
            true,
            true
        );

        $this->beConstructedWith($tableParser);

        $pluginList = <<<EOL
+-------------------+----------------------------------+---------+-----------------------+--------+-----------+
| Plugin            | Label                            | Version | Author                | Active | Installed |
+-------------------+----------------------------------+---------+-----------------------+--------+-----------+
| BenroeTawk        | Tawk Chat Widget                 | 2.0.0   | Code-Cap              | Yes    | Yes       |
| BestitAmazonPay   | Amazon Pay and Login with Amazon | 9.4.1   | best it GmbH & Co. KG | Yes    | Yes       |
| BrickSaleschannel | Brickfox                         | 6.0.12  | brickfox GmbH         | Yes    | Yes       |
+-------------------+----------------------------------+---------+-----------------------+--------+-----------+
EOL;

        $this->parsePluginVersionsFromPluginList($pluginList);

        $this->getPluginVersion('BenroeTawk')
            ->shouldReturn('2.0.0');

        $this->getPluginVersion('BestitAmazonPay')
            ->shouldReturn('9.4.1');

        $this->getPluginVersion('BrickSaleschannel')
            ->shouldReturn('6.0.12');
    }

    private function initializePluginVersions(TableParser $tableParser): void
    {
        $tableParser->parse(Argument::any())
            ->willReturn([
                [
                    0 => 'asdfPlugin',
                    2 => '1.2.3',
                ],
                [
                    0 => 'nlxTest',
                    2 => '5.0.0',
                ],
                [
                    0 => 'downgraded',
                    2 => '2.1.2',
                ],
            ]);

        $this->parsePluginVersionsFromPluginList('');
    }
}
