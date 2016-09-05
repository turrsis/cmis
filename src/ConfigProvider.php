<?php
namespace Turrsis\Cmis;

class ConfigProvider
{
    /**
     * Retrieve turrsis-cmis default configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Retrieve turrsis-cmis default dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'aliases' => [
                'config' => 'Config',
            ],
            'abstract_factories' => [
                RepositoryAbstractFactory::class,
            ],
        ];
    }
}
