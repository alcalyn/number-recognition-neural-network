<?php

namespace Alcalyn\NeuralNetworkApi\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Alcalyn\NeuralNetwork\Network;
use Alcalyn\NeuralNetworkApi\Service\ImageMatrix;

class TestNetworkCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ImageMatrix
     */
    private $imageMatrix;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, ImageMatrix $imageMatrix)
    {
        parent::__construct();

        $this->om = $om;
        $this->imageMatrix = $imageMatrix;
    }

    /**
     * {@InheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('neural:network:test')
            ->setDescription('Test network in 1000 samples.')
        ;
    }

    /**
     * {@InheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $samplesBinFile = __DIR__.'/../../../var/cache/samples.bin';

        $output->writeln('Loading samples...');
        $samples = unserialize(file_get_contents($samplesBinFile));
        $output->writeln(count($samples).' samples loaded.');

        $output->writeln($this->imageMatrix->toString($samples[array_rand($samples)][0]));

        $output->writeln('Loading network...');
        $network = $this->om->getRepository(Network::class)->findOneByName('number-recognition');

        $output->writeln('Testing on 1000 samples, displaying only fails...');

        $success = 0;

        for ($i = 0; $i < 1000; $i++) {
            $sample = $samples[$i * 60];
            $out = $network->pulseInput($sample[0], $sample[1]);

            if (0 === $i % 100) {
                $output->writeln('');
            }

            if ($this->imageMatrix->isSuccess($out, $sample[1])) {
                $success++;
            } else {
                $output->writeln($this->imageMatrix->toString($sample[0]));
            }
        }

        $output->writeln('');
        $output->writeln('Success: '.($success / 10).' %');
    }
}
