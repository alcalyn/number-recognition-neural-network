<?php

namespace Alcalyn\NeuralNetworkApi\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Alcalyn\NeuralNetwork\Network;
use Alcalyn\NeuralNetworkApi\Service\ImageMatrix;

class ParseSampleCommand extends Command
{
    /**
     * @var ImageMatrix
     */
    private $imageMatrix;

    /**
     * @param ImageMatrix $imageMatrix
     */
    public function __construct(ImageMatrix $imageMatrix)
    {
        parent::__construct();

        $this->imageMatrix = $imageMatrix;
    }

    /**
     * {@InheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('neural:sample:parse')
            ->setDescription('Parse numbers image to matrices.')
        ;
    }

    /**
     * {@InheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $samples = [];
        $imagesDir = __DIR__.'/../Resources/images-samples';
        $samplesBinFile = __DIR__.'/../../../var/cache/samples.bin';

        for ($n = 0; $n <= 9; $n++) {
            $filename = $imagesDir."/$n.jpg";
            $output->writeln("Parsing $n in $filename...");

            $expected = $this->imageMatrix->number($n);
            $img = imagecreatefromjpeg($filename);
            $imgSize = getimagesize($filename);
            list($width, $height) = $imgSize;

            for ($i = 0; $i < $width; $i += 28) {
                for ($j = 0; $j < $height; $j += 28) {
                    $matrix = $this->imageMatrix->sampleImageToMatrix($img, $i, $j);

                    if (!$this->imageMatrix->matrixIsEmpty($matrix)) {
                        $samples []= [$matrix, $expected];
                    }
                }
            }
        }

        $output->writeln(count($samples).' sample generated.');

        $sample = $samples[0];
        $output->writeln(print_r([$sample[0], $sample[1]], true));
        $output->writeln($this->imageMatrix->toString($samples[0][0]));

        $output->writeln('Dumping samples...');
        file_put_contents($samplesBinFile, serialize($samples));
        $output->writeln(count($samples).' dumped to samples.bin');
    }
}
