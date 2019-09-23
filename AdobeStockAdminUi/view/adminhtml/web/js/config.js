/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';

    return {
        mediaGallerySelector: '.media-gallery-modal:has(#search_adobe_stock)',
        adobeStockModalSelector: '#adobe-stock-images-search-modal',
        downloadPreviewUrl: 'adobe_stock/preview/download',
        licenseAndDownloadUrl: 'adobe_stock/license/license',
        quotaUrl: 'adobe_stock/license/getquota',
        seriesUrl: 'adobe_stock/preview/series'
    };
});
