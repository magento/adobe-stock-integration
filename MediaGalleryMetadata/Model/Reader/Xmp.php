<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Reader;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataReaderInterface;
use Magento\MediaGalleryMetadata\Model\Reader\File as FileReader;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;

/**
 * XMP Reader
 */
class Xmp implements MetadataReaderInterface
{
    private const XMP_DATA_START_POSITION = 29;
    private const XMP_XPATH_SELECTOR_TITLE = '//dc:title/rdf:Alt/rdf:li';
    private const XMP_XPATH_SELECTOR_DESCRIPTION = '//dc:description/rdf:Alt/rdf:li';
    private const XMP_XPATH_SELECTOR_KEYWORDS = '//dc:subject/rdf:Bag/rdf:li';

    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @param MetadataInterfaceFactory $metadataFactory
     */
    public function __construct(MetadataInterfaceFactory $metadataFactory, FileReader $reader)
    {
        $this->metadataFactory = $metadataFactory;
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): MetadataInterface
    {
        $file = $this->reader->execute($path);
        return $this->extractData($file);
    }

    /**
     * Extract metadata from the file
     *
     * @param FileInterface $file
     * @return MetadataInterface
     */
    public function extractData(FileInterface $file): MetadataInterface
    {
        foreach ($file->getSegments() as $segment) {
            if ($this->isXmpSegment($segment)) {
                return $this->getMetadata($this->getXmpData($segment));
            }
        }
        return $this->metadataFactory->create([
            'title' => '',
            'description' => '',
            'keywords' => []
        ]);
    }

    /**
     * Parse metadata
     *
     * @param string $data
     * @return MetadataInterface
     */
    private function getMetadata(string $data): MetadataInterface
    {
        $xml = simplexml_load_string($data);
        $namespaces = $xml->getNamespaces(true);

        foreach ($namespaces as $prefix => $url) {
            $xml->registerXPathNamespace($prefix, $url);
        }

        $keywords = array_map(
            function (\SimpleXMLElement $element): string {
                return (string) $element;
            },
            $xml->xpath(self::XMP_XPATH_SELECTOR_KEYWORDS)
        );

        $description = implode(' ', $xml->xpath(self::XMP_XPATH_SELECTOR_DESCRIPTION));
        $title = implode(' ', $xml->xpath(self::XMP_XPATH_SELECTOR_TITLE));

        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        ]);
    }

    /**
     * @param SegmentInterface $segment
     * @return bool
     */
    private function isXmpSegment(SegmentInterface $segment): bool
    {
        return $segment->getName() === 'APP1'
            && strncmp($segment->getData(), "http://ns.adobe.com/xap/1.0/\x00", self::XMP_DATA_START_POSITION) == 0;
    }

    /**
     * @param SegmentInterface $segment
     * @return string
     */
    private function getXmpData(SegmentInterface $segment): string
    {
        return substr($segment->getData(), self::XMP_DATA_START_POSITION);
    }
}
