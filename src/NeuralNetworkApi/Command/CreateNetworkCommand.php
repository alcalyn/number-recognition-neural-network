<?php

namespace Alcalyn\NeuralNetworkApi\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Alcalyn\NeuralNetwork\Network;

class CreateNetworkCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct();

        $this->om = $om;
    }

    /**
     * {@InheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('neural:network:create')
            ->setDescription('Initialize the neural network.')
        ;
    }

    /**
     * {@InheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $network = new Network([196, 100, 10]);

        $network->e = 2;
        $network->name = 'number-recognition';

        $this->om->persist($network);
        $this->om->flush();
    }
}
