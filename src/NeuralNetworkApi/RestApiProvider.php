<?php

namespace Alcalyn\NeuralNetworkApi;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Alcalyn\NeuralNetworkApi\Event\HelloEvent;

class RestApiProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
    }
}
