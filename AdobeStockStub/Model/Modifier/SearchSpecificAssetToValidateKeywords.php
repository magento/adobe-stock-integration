<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**Volumes/Projects/apache/magento2/setup/src/Magento/Setup/Module.php
 * Modify File if the search request contains specified image id to validate keywords appearance.
 */
class SearchSpecificAssetToValidateKeywords implements ModifierInterface
{
    /**
     * Modify file data if request is on specified media id.
     *
     * @see [#Story 33] User searches for an image by its keyword tag
     * @param array $files
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array
    {
        return (preg_match('(\[words\]=273672939)', $url)) ?
            $this->setSpecifiedAssetParametersForTest($files)
            : $files;
    }

    /**
     * Set specified asset for test.
     *
     * @param array $files
     *
     * @return array
     */
    private function setSpecifiedAssetParametersForTest(array $files): array
    {
        $asset = reset($files);
        $asset['keywords'][0] = ['name' => 'accessory'];

        return [$asset];
    }
}
