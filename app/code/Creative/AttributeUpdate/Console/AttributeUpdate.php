<?php
namespace Creative\AttributeUpdate\Console;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AttributeUpdate extends Command
{
    const SKU = 'sku';
    const PRODUCT_NAME = 'name';
    const PRODUCT_DESCRIPTION = 'description';
    const PRODUCT_PRICE = 'price';
    const PRODUCT_QTY = 'qty';
    const PRODUCT_ISINSTOCK = 'is_in_stock';

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     * @param \Magento\Framework\App\State $state
     * @param string|null $name
     */
    public function __construct(
        ProductRepository $productRepository,
        \Magento\Framework\App\State $state,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->productRepository = $productRepository;
        $this->state = $state;
    }

    /**
     * @return void
     */
    protected function configure()
    {

        $this->setName('creative:attributeupdate');
        $this->setDescription('Attribute update by command line');
        $this->addOption(
            self::SKU,
            null,
            InputOption::VALUE_REQUIRED,
            'Sku'
        );
        $this->addOption(
            self::PRODUCT_NAME,
            null,
            InputOption::VALUE_OPTIONAL,
            'Name'
        );
        $this->addOption(
            self::PRODUCT_DESCRIPTION,
            null,
            InputOption::VALUE_OPTIONAL,
            'Description'
        );
        $this->addOption(
            self::PRODUCT_PRICE,
            null,
            InputOption::VALUE_OPTIONAL,
            'Price'
        );
        $this->addOption(
            self::PRODUCT_QTY,
            null,
            InputOption::VALUE_OPTIONAL,
            'Qty'
        );
        $this->addOption(
            self::PRODUCT_ISINSTOCK,
            null,
            InputOption::VALUE_OPTIONAL,
            'Is in Stock'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return $this|int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($sku = $input->getOption(self::SKU)) {

            /** @var ProductInterfaceFactory $product */
            $product = $this->productRepository->get($sku);

            if($product){

                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
                if ($name = $input->getOption(self::PRODUCT_NAME)) {
//                    $output->writeln("name " . $input->getOption(self::PRODUCT_NAME) . " ");
//                    $product->setName($name);
//                    $product->setData('name',$name);
                    $product->addAttributeUpdate('name',$name,0);
                }
                if ($description = $input->getOption(self::PRODUCT_DESCRIPTION)) {
//                    $output->writeln("desc " . $input->getOption(self::PRODUCT_DESCRIPTION) . " ");
//                    $product->setDescription($description);
                    $product->addAttributeUpdate('description',$description,0);
                }
                if ($input->getOption(self::PRODUCT_PRICE) != '') {
//                    $output->writeln("price " . $input->getOption(self::PRODUCT_PRICE) . " ");
                    $price = $input->getOption(self::PRODUCT_PRICE);
                    $product->setPrice($price);
                }
                if ($input->getOption(self::PRODUCT_QTY) != '') {
//                    $output->writeln("price " . $input->getOption(self::PRODUCT_QTY) . " ");
                    $qty = $input->getOption(self::PRODUCT_QTY);
                    $product->setStockData(
                        array(
                            'qty' => $qty
                        )
                    );
                    if ($input->getOption(self::PRODUCT_ISINSTOCK) != '') {
//                    $output->writeln("is in stock " . $input->getOption(self::PRODUCT_ISINSTOCK) . " ");

                        $isInStock = $input->getOption(self::PRODUCT_ISINSTOCK);
                        $product->setStockData(
                            array(
                                'is_in_stock' => $isInStock,
                                'qty' => $qty
                            )
                        );
                    }
                } else if ($input->getOption(self::PRODUCT_ISINSTOCK) != '') {
//                    $output->writeln("is in stock " . $input->getOption(self::PRODUCT_ISINSTOCK) . " ");

                    $isInStock = $input->getOption(self::PRODUCT_ISINSTOCK);
                    $product->setStockData(
                        array(
                            'is_in_stock' => $isInStock,
                        )
                    );
                }
                $this->productRepository->save($product);

                $output->writeln("for " . $sku . " and Product is updated.");
            }else{
                $output->writeln("Product not found.");
            }
        } else {
            $output->writeln("Please Add SKU.");
        }
        return $this;
    }
}
