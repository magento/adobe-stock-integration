<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Request\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source) : array
    {
        $result = [];
        /** @var \DOMNode $request */
        foreach ($source->documentElement->childNodes as $request) {
            if ($request->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $requestName = $request->attributes->getNamedItem('name')->nodeValue;

            /** @var \DOMNode $parameter */
            foreach ($request->childNodes as $parameter) {
                if ($parameter->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                $nodeName = $parameter->nodeName;
                switch ($nodeName) {
                    case 'from':
                    case 'size':
                        $result[$requestName][$nodeName] = $parameter->nodeValue;
                        break;
                    case 'filters':
                    case 'resultColumns':
                        $result[$requestName][$nodeName] = $this->processItem($parameter);
                        break;
                }
            }
        }
        return $result;
    }

    /**
     * @param \DOMNode $parameter
     * @return array
     */
    private function processItem(\DOMNode $parameter) : array
    {
        $items = [];

        /** @var \DOMNode $item */
        foreach ($parameter->childNodes as $item) {
            if ($item->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $itemAttributes = [];
            foreach ($item->attributes as $attribute) {
                if ($attribute->nodeType != XML_ATTRIBUTE_NODE) {
                    continue;
                }
                $itemAttributes[$attribute->nodeName] = $attribute->nodeValue;
            }
            $items[] = $itemAttributes;
        }

        return $items;
    }
}
