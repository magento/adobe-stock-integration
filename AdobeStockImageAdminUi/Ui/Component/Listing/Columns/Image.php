<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Image column
 */
class Image extends Column
{
    private const ID = 'id';
    private const URL = 'thumbnail_240_url';

    /**
     * @inheritdoc
     */
    public function prepare(): void
    {
        parent::prepare();
        $this->setData(
            'config',
            array_replace_recursive(
                (array) $this->getData('config'),
                [
                    'fields' => [
                        'id' => self::ID,
                        'url' => self::URL
                    ]
                ]
            )
        );
    }
}
