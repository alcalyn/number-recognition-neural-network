<?php

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Application as ConsoleApplication;
use Application as SilexApplication;

class Console extends ConsoleApplication
{
    /**
     * @var SilexApplication
     */
    private $silexApplication;

    /**
     * Console application constructor.
     *
     * @param SilexApplication $silexApplication
     */
    public function __construct(SilexApplication $silexApplication)
    {
        parent::__construct('My Sandstone application');

        $this->silexApplication = $silexApplication;
        $this->silexApplication->boot();

        $this->registerDoctrineCommands();
        $this->registerNeuralNetworkCommands();
    }

    private function registerDoctrineCommands()
    {
        $em = $this->silexApplication['orm.em'];

        // Register Doctrine ORM commands
        $helperSet = new HelperSet(array(
            'db' => new ConnectionHelper($em->getConnection()),
            'em' => new EntityManagerHelper($em)
        ));

        $this->setHelperSet($helperSet);
        ConsoleRunner::addCommands($this);
    }

    private function registerNeuralNetworkCommands()
    {
        $em = $this->silexApplication['orm.em'];
        $imageMatrix = $this->silexApplication['app.image_matrix'];

        $this->addCommands([
            new \Alcalyn\NeuralNetworkApi\Command\ParseSampleCommand($imageMatrix),
            new \Alcalyn\NeuralNetworkApi\Command\CreateNetworkCommand($em),
            new \Alcalyn\NeuralNetworkApi\Command\TrainNetworkCommand($em, $imageMatrix),
            new \Alcalyn\NeuralNetworkApi\Command\TestNetworkCommand($em, $imageMatrix),
        ]);
    }
}
