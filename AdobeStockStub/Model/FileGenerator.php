<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\AdobeStockStub\Model\Generator\GeneratorInterface;
use Magento\AdobeStockStub\Model\Modifier\ModifierInterface;
use Magento\Framework\DataObject;

/**
 * Generate a stub Adobe Stock API Asset file object based on the stub parameters.
 */
class FileGenerator
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var array
     */
    private $fileAttributeGenerators;

    /**
     * @param FileFactory $fileFactory
     * @param array $fileAttributeGenerators
     */
    public function __construct(
        FileFactory $fileFactory,
        array $fileAttributeGenerators
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileAttributeGenerators = $fileAttributeGenerators;
    }

    /**
     * Generate Files based on the File attribute generator and modifiers.
     *
     * @param array $modifiers
     * @param int $filesAmount
     *
     * @return array
     */
    public function generate(array $modifiers, int $filesAmount): array
    {
        $files = [];
        $iterator = 0;
        do {
            /** @var DataObject $file */
            $file = $this->fileFactory->generate([]);
            foreach ($this->fileAttributeGenerators as $attributeGenerator) {
                if ($attributeGenerator instanceof GeneratorInterface) {
                    $file = $attributeGenerator->generate($file);
                }
            }
            $files[] = $file;
            $iterator++;
        } while ($filesAmount > $iterator);

        foreach ($files as $file) {
            /** @var ModifierInterface $modifier */
            foreach ($modifiers as $modifier) {
                $modifier->modifyValue($file);
            }
        }

        return $files;
    }
}
