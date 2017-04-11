<?php

namespace Alcalyn\NeuralNetworkApi;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Alcalyn\NeuralNetworkApi\Service\ImageMatrix;

class Provider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app['app.image_matrix'] = function () {
            return new ImageMatrix();
        };

        $app->extend('doctrine.mappings', function ($mappings, $app) {
            $mappings []= [
                'type' => 'yml',
                'namespace' => 'Alcalyn\\NeuralNetwork\\',
                'path' => $app['project.root'].'/vendor/alcalyn/neural-network/src/Mapping',
            ];

            return $mappings;
        });
    }
}
