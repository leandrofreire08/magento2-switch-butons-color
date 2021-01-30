<?php
namespace Freire\SwitchButtonsColor\Console\Command;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class SwitchButtonColor extends Command
{
    const COLOR = 'color';
    const STORE_ID = 'store_id';
    const CSS_DIR = "/frontend/web/css/";

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $ioAdapter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * SwitchButtonColor constructor.
     * @param Reader $moduleReader
     * @param File $ioAdapter
     * @param StoreManagerInterface $storeManager
     * @param WriterInterface $configWriter
     * @param string|null $name
     */
    public function __construct(
        Reader $moduleReader,
        File $ioAdapter,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        string $name = null
    ) {
        parent::__construct($name);
        $this->moduleReader = $moduleReader;
        $this->ioAdapter = $ioAdapter;
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('freire:swith-button-color');
        $this->setDescription('Switch all primary button class to the given hex color');
        $this->addOption(
            self::COLOR,
            null,
            InputOption::VALUE_REQUIRED,
            'Hex color, i.e: fffff (without #)'
        );

        $this->addOption(
            self::STORE_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Store ID'
        );

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $color = $input->getOption(self::COLOR);
        $storeId = $input->getOption(self::STORE_ID);

        if (!$color || !$storeId) {
            return $output->writeln("Parameters " . self::COLOR . " and " . self::STORE_ID . " are required!");
        }

        if (!$this->isValidStoreId($storeId)) {
            return $output->writeln("The store_id provided do not exist!");
        }

        if (!$this->isValidColor($color)) {
            return $output->writeln("The color provided is invalid!");
        }

        $file = $this->getModuleCSSDirectory() . "buttons.css";
        $cssFile = $this->ioAdapter->read($file);
        $newHex = preg_replace('/#[0-9a-f]{6}|#[0-9a-f]{3}/i', "#" . $color, $cssFile);
        $this->ioAdapter->write($file, $newHex);

        $this->configWriter->save(
            'switchbuttonscolor/general/store_id',
            $storeId,
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $scopeId = 0
        );

        $this->flushCache($output);
    }

    /**
     * @return string
     */
    private function getModuleCSSDirectory()
    {
        return $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Freire_SwitchButtonsColor'
        ) . self::CSS_DIR;
    }

    /**
     * @param $storeId
     * @return bool
     */
    private function isValidStoreId($storeId)
    {
        $stores = $this->storeManager->getStores();

        $storeIds = [];
        foreach ($stores as $store) {
            $storeIds[] = $store->getId();
        }

        if (in_array($storeId, $storeIds)) {
            return true;
        }

        return false;
    }

    private function isValidColor($color)
    {
        $color = ltrim($color, '#');

        if (ctype_xdigit($color) && (strlen($color) == 6 || strlen($color) == 3)) {
            return true;
        }

        return false;
    }

    private function flushCache($output)
    {
        $arguments = new ArrayInput(['command' => 'cache:flush']);
        $this->getApplication()->run($arguments, $output);
    }
}
