<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Writer;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;
use Magento\MediaGalleryMetadata\Model\Reader\File as FileReader;
use Magento\MediaGalleryMetadata\Model\Writer\File as FileWriter;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;

/**
 * XMP Writer
 */
class Xmp implements MetadataWriterInterface
{
    private const XMP_SEGMENT_NAME = 'APP1';
    private const XMP_SEGMENT_START = "http://ns.adobe.com/xap/1.0/\x00";
    private const XMP_DATA_START_POSITION = 29;
    private const XMP_XPATH_SELECTOR_TITLE = '//dc:title/rdf:Alt/rdf:li';
    private const XMP_XPATH_SELECTOR_DESCRIPTION = '//dc:description/rdf:Alt/rdf:li';
    private const XMP_XPATH_SELECTOR_KEYWORDS = '//dc:subject/rdf:Bag';
    private const XMP_XPATH_SELECTOR_KEYWORDS_EACH = '//dc:subject/rdf:Bag/rdf:li';

    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var FileWriter
     */
    private $writer;

    /**
     * @var SegmentInterfaceFactory
     */
    private $segmentFactory;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @param FileReader $reader
     * @param FileWriter $writer
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     */
    public function __construct(
        FileReader $reader,
        FileWriter $writer,
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        $file = $this->reader->execute($path);
        $updateFile = $this->addMetadata($file, $metadata);
        $this->writer->execute($updateFile);

    }

    /**
     * Add metadata to the file
     *
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     * @return FileInterface
     */
    private function addMetadata(FileInterface $file, MetadataInterface $metadata): FileInterface
    {
        $segments = $file->getSegments();
        foreach ($segments as $key => $segment) {
            if ($this->isXmpSegment($segment)) {
                $segments[$key] = $this->updateSegment($segment, $metadata);
            }
        }
        return $this->fileFactory->create([
            'path' => $file->getPath(),
            'compressedImage' => $file->getCompressedImage(),
            'segments' => $segments
        ]);
    }

    /**
     * Add metadata to the segment
     *
     * @param SegmentInterface $segment
     * @return SegmentInterface
     */
    public function updateSegment(SegmentInterface $segment, MetadataInterface $metadata): SegmentInterface
    {
        return $this->segmentFactory->create([
            'name' => $segment->getName(),
            'dataStart' => $segment->getDataStart(),
            'data' => $this->updateData($segment->getData(), $metadata)
        ]);
    }

    /**
     * Parse metadata
     *
     * @param string $data
     * @return MetadataInterface
     */
    private function updateData(string $data, MetadataInterface $metadata): string
    {
        $start = substr($data, 0, self::XMP_DATA_START_POSITION);
        $xmpData = substr($data, self::XMP_DATA_START_POSITION);
        $xml = simplexml_load_string($xmpData);
        $namespaces = $xml->getNamespaces(true);

        foreach ($namespaces as $prefix => $url) {
            $xml->registerXPathNamespace($prefix, $url);
        }

        $this->setValueByXpath($xml, self::XMP_XPATH_SELECTOR_TITLE, $metadata->getTitle());
        $this->setValueByXpath($xml, self::XMP_XPATH_SELECTOR_DESCRIPTION, $metadata->getDescription());
        $this->updateKeywords($xml, $metadata->getKeywords());

        $data = $xml->asXML();
        return $start . $data;
    }

    /**
     * Update keywords
     *
     * @param \SimpleXMLElement $xml
     * @param array $keywords
     */
    private function updateKeywords(\SimpleXMLElement $xml, array $keywords): void
    {
        foreach ($xml->xpath(self::XMP_XPATH_SELECTOR_KEYWORDS_EACH) as $keywordElement) {
            unset($keywordElement[0]);
        }

        foreach ($xml->xpath(self::XMP_XPATH_SELECTOR_KEYWORDS) as $element) {
            foreach ($keywords as $keyword) {
                $element->addChild('rdf:li', $keyword);
            }
        }
    }

    /**
     * Set value to xml node by xpath
     *
     * @param \SimpleXMLElement $xml
     * @param string $xpath
     * @param string $value
     */
    private function setValueByXpath(\SimpleXMLElement $xml, string $xpath, string $value): void
    {
        foreach ($xml->xpath($xpath) as $element) {
            $element[0] = $value;
        }
    }

    /**
     * Check if segment contains XMP data
     *
     * @param SegmentInterface $segment
     * @return bool
     */
    private function isXmpSegment(SegmentInterface $segment): bool
    {
        return $segment->getName() === self::XMP_SEGMENT_NAME
            && strncmp($segment->getData(), self::XMP_SEGMENT_START, self::XMP_DATA_START_POSITION) == 0;
    }
}
