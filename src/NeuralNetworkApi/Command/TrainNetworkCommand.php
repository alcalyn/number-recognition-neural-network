<?php

namespace Alcalyn\NeuralNetworkApi\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Alcalyn\NeuralNetwork\Network;
use Alcalyn\NeuralNetworkApi\Service\ImageMatrix;

class TrainNetworkCommand extends Command
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
            ->setName('neural:network:train')
            ->setDescription('Train to recognized numbers on 60000 samples.')
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


        $output->writeln('Train...');

        $success = 0;

        for ($i = 0; $i < 60000; $i++) {
            $sample = $samples[($i * 6001) % 60000];
            $out = $network->trainInput($sample[0], $sample[1]);

            if ($this->imageMatrix->isSuccess($out, $sample[1])) {
                $success++;
                $output->write('.');
            } else {
                $output->write('F');
            }

            if (0 === $i % 100) {
                $output->writeln('');
                $output->writeln("$i / ".count($samples));
                $output->writeln("Successes: $success %");
                $output->writeln('e = '.round($network->e, 3));
                $success = 0;

                if ($network->e > 1) {
                    $network->e *= 0.99;
                } elseif ($network->e > 0.2) {
                    $network->e *= 0.999;
                }

                if (0 === $i % 1000) {
                    $output->write('Persisting neural network...');
                    $this->om->flush();
                    $output->writeln(' OK');
                }
                $output->writeln('');

            }
        }

        $output->writeln('');
        $output->write('Persisting neural network...');
        $this->om->flush();
        $output->writeln(' OK');
    }
}
