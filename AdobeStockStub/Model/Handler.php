<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\AdobeStockStub\Model\Modifier\ModifierInterface;
/**
 * Handle request parameters to instruct response generator.
 */
class Handler
{
    /**
     * @var FileGenerator
     */
    private $fileGenerator;

    /**
     * @var array
     */
    private $modifiers;

    /**
     * @param FileGenerator $fileGenerator
     * @param array $modifiers
     */
    public function __construct(
        FileGenerator $fileGenerator,
        array $modifiers
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->modifiers = $modifiers;

    }

    /**
     * Parse URL to get request parameters, handle them and create a response array.
     *
     * @param string $url
     * @param array $headers
     *
     * @return array []
     */
    public function generateResponse(string $url, array $headers): array
    {
        $url = urldecode($url);
        $files = $this->fileGenerator->generate($this->getLimit($url));
            foreach ($this->modifiers as $modifier) {
                if ($modifier instanceof ModifierInterface) {
                    $files = $modifier->modify($files, $url, $headers);
                    if (empty($files)) {
                        break;
                    }
                }
            }

        return [
            'nb_results' => count($files),
            'files' => $files
        ];
    }

    /**
     * Get the limit from the request URL.
     *
     * @param string $url
     *
     * @return int
     */
    private function getLimit(string $url): int
    {
        $matches = [];
        preg_match_all("/(?<=\[limit\]=).[0-9]/", $url, $matches);
        return !empty($matches) ?
            (int)$matches[0][0]
            : 0;
    }
}
