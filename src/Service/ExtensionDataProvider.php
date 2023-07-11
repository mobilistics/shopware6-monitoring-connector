<?php

namespace MobilisticsGmbH\MamoConnector\Service;

use Generator;
use MobilisticsGmbH\MamoConnector\Dto\Plugin;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginEntity;

final class ExtensionDataProvider
{
    public function __construct(
        private readonly EntityRepository $pluginRepository,
    ) {
    }

    /**
     * @return Generator<Plugin>
     */
    public function loadExtensionData(): Generator
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', '1'));

        /** @var PluginEntity[] $plugins */
        $plugins = $this->pluginRepository->search($criteria, Context::createDefaultContext());

        foreach ($plugins as $plugin) {
            yield new Plugin(
                technicalName: $plugin->getName(),
                version: $plugin->getVersion(),
            );
        }
    }
}
