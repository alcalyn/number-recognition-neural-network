<?php

namespace Alcalyn\NeuralNetworkApi\Topic;

use Ratchet\Wamp\WampConnection;
use Eole\Sandstone\Websocket\Topic;
use Alcalyn\NeuralNetwork\Network;
use Alcalyn\NeuralNetworkApi\Service\ImageMatrix;

class NetworkTopic extends Topic
{
    /**
     * @var Network
     */
    private $network;

    /**
     * @var ImageMatrix
     */
    private $imageMatrix;

    /**
     * @param string $topicPath
     * @param Network $network
     * @param ImageMatrix $imageMatrix
     */
    public function __construct($topicPath, Network $network, ImageMatrix $imageMatrix)
    {
        parent::__construct($topicPath);

        $this->network = $network;
        $this->imageMatrix = $imageMatrix;
    }

    /**
     * Broadcast message to each subscribing client.
     *
     * {@InheritDoc}
     */
    public function onPublish(WampConnection $conn, $topic, $event)
    {
        $conn->event($topic, 'hello '.$event);

        $img = imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,', '', $event)));
        $in = $this->imageMatrix->imageToMatrix($img);
        $out = $this->network->pulseInput($in);

        $conn->event($topic, $this->imageMatrix->toString($in));

        $conn->event($topic, [
            'type' => 'recognize',
            'recognize' => $out,
        ]);
    }
}
