<?php

namespace Alcalyn\NeuralNetworkApi;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Alcalyn\NeuralNetwork\Network;
use Alcalyn\NeuralNetworkApi\Service\ImageMatrix;
use Alcalyn\NeuralNetworkApi\Topic\NetworkTopic;

class WebsocketProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->topic('network', function ($topicPattern) use ($app) {
            $network = $app['orm.em']
                ->getRepository(Network::class)
                ->findOneByName('number-recognition')
            ;

            $imageMatrix = new ImageMatrix();

            return new NetworkTopic($topicPattern, $network, $imageMatrix);
        });
    }
}
