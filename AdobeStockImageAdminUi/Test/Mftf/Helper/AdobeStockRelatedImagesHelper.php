<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Mftf\Helper;

use Facebook\WebDriver\WebDriverBy;
use Magento\FunctionalTestingFramework\Helper\Helper;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;

/**
 * Helper for the Adobe Stock image preview "related images" tabs.
 */
class AdobeStockRelatedImagesHelper extends Helper
{
    /**
     * Click the "More from this model" tab and verify the model filter gets applied,
     * but only when the previewed image actually has a model tab.
     *
     * Adobe Stock only attaches "same model" matches to images with a human subject,
     * so a previewed image without one (e.g. a landscape or architecture photo)
     * legitimately has no model tab. That is a valid outcome, not a test failure.
     *
     * @param string $modelTabSelector
     * @param string $modelSeeMoreSelector
     * @param string $filtersToggleSelector
     * @param string $recordsFoundSelector
     * @param string $fileAttributeValue
     * @param string $countWithoutModelFilter
     *
     * @return void
     */
    public function verifySameModelFilterIfAvailable(
        string $modelTabSelector,
        string $modelSeeMoreSelector,
        string $filtersToggleSelector,
        string $recordsFoundSelector,
        string $fileAttributeValue,
        string $countWithoutModelFilter
    ): void {
        /** @var MagentoWebDriver $webDriverModule */
        $webDriverModule = $this->getModule('\Magento\FunctionalTestingFramework\Module\MagentoWebDriver');

        if (empty($webDriverModule->webDriver->findElements(WebDriverBy::xpath($modelTabSelector)))) {
            codecept_debug(
                'Previewed image has no "same model" matches - skipping "More from this model" verification.'
            );

            return;
        }

        $webDriverModule->click($modelTabSelector);
        $webDriverModule->click($modelSeeMoreSelector);
        $webDriverModule->waitForPageLoad();
        $webDriverModule->click($filtersToggleSelector);

        // Mirrors AdminAdobeStockFilterSection.activeFilter, which cannot be resolved
        // with a runtime value outside of a declarative action group.
        $activeFilterSelector = sprintf(
            "//*[@id='adobe-stock-images-search-modal']//div[@class='admin__current-filters-list-wrap']" .
            "//span[contains( ., '%s')]",
            $fileAttributeValue
        );
        $webDriverModule->seeElement($activeFilterSelector);

        $countWithAppliedFilter = $webDriverModule->grabTextFrom($recordsFoundSelector);
        $this->assertNotEquals($countWithoutModelFilter, $countWithAppliedFilter);
    }
}
