<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

/**
 * Generate a stub Adobe Stock API Asset file object based on the search query.
 */
class FileGenerator
{
    /**
     * Generate an array of Adobe Stock stub files array based on the query.
     *
     * @param array $query
     *
     * @return array
     */
    public function generate(array $query): array
    {
        /**
         * @TODO move file array to the DI with predefined data where possible.
         */
        $files = [];
        $i = 0;
        $data = $query['search_parameters'];
        do {
            $files[] = [
                'id' => $this->getId($data),
                'comp_url' => $this->getCompUrl($data),
                'thumbnail_240_url' => $this->get240Url($data),
                'width' => $this->getWidth($data),
                'height' => $this->getHeight($data),
                'thumbnail_500_url' => $this->get500Url($data),
                'title' => $this->getTitle($data),
                'creator_id' => $this->getCreatorId($data),
                'creator_name' => $this->getCreatorName($data),
                'creation_date' => $this->getCreationDate($data),
                'country_name' => $this->getCountryName($data),
                'category' => $this->getCategory($data),
                'keywords' => $this->getKeywords($data),
                'media_type_id' => 1,
                'content_type' => 'image/jpeg',
                'details_url' => 'https//adobe.stock.stub/details',
                'premium_level_id' => 0,
            ];
            $i++;
        } while ($data['limit'] > $i);

        return $files;
    }

    /**
     * If id is set as a search request parameter use it. Otherwise 1.
     *
     * @param array $data
     *
     * @return int
     */
    private function getId(array $data): int
    {
        return isset($data['id']) ? (int)$data['id'] : rand(1, 999);
    }

    /**
     * If comp url is set as a search request parameter use it. Otherwise predefined url.
     *
     * @param array $data
     *
     * @return string
     */
    private function getCompUrl(array $data): string
    {
        return isset($data['comp_url']) ? $data['comp_url'] : 'https//adobe.stock.stub';
    }

    /**
     * If 240 thumbnail is set as a search request parameter use it. Otherwise predefined url.
     *
     * @param array $data
     *
     * @return string
     */
    private function get240Url(array $data): string
    {
        return isset($data['thumbnail_240_url']) ?
            $data['thumbnail_240_url']
            : 'https//adobe.stock.stub/240';
    }

    /**
     * If height is set as a search request parameter set it. Otherwise 1.
     *
     * @param array $data
     *
     * @return int
     */
    private function getHeight(array $data): int
    {
        return isset($data['height']) ? (int)$data['height'] : rand(1, 999);
    }

    /**
     * If width is set as a search request parameter set it. Otherwise 1.
     *
     * @param array $data
     *
     * @return int
     */
    private function getWidth(array $data): int
    {
        return isset($data['width']) ? (int)$data['width'] : rand(1, 999);
    }

    /**
     * If 500 thumbnail is set as a search request parameter use it. Otherwise predefined url.
     *
     * @param array $data
     *
     * @return string
     */
    private function get500Url(array $data): string
    {
        return isset($data['thumbnail_250_url']) ? $data['thumbnail_250_url'] : 'https//adobe.stock.stub/500';
    }

    /**
     * If title is set as a search request parameter use it. Otherwise predefined title.
     *
     * @param array $data
     *
     * @return string
     */
    private function getTitle(array $data): string
    {
        return isset($data['title']) ? $data['title'] : 'Adobe Stock Stub file';
    }

    /**
     * If creator id is set as a search request parameter use it. Otherwise 1.
     *
     * @param array $data
     *
     * @return int
     */
    private function getCreatorId(array $data): int
    {
        return isset($data['creator_id']) ? (int)$data['creator_id'] : rand(1, 999);
    }

    /**
     * If creator name is set as a search request parameter use it. Otherwise predefined name.
     *
     * @param array $data
     *
     * @return string
     */
    private function getCreatorName(array $data): string
    {
        return isset($data['name']) ? $data['name'] : 'Adobe Stock file creator name';
    }

    /**
     * If creation date is set as a search request parameter use it. Otherwise predefined value.
     *
     * @param array $data
     *
     * @return string
     */
    private function getCreationDate(array $data): string
    {
        return isset($data['creation_date']) ? $data['creation_date'] : '2020-03-11 12:50:05.542333';
    }

    /**
     * If country name is set as a search request parameter use it. Otherwise predefined value.
     *
     * @param array $data
     *
     * @return string
     */
    private function getCountryName(array $data): string
    {
        return isset($data['country_name']) ? $data['country_name'] : 'Adobe Stock Stub file country name';
    }

    /**
     * If category is set as a search request parameter use it. Otherwise predefined value.
     *
     * @param array $data
     *
     * @return array
     */
    private function getCategory(array $data): array
    {
        return [
                'id' =>  isset($data['id']) ? $data['id'] : 1,
                'name' => 'Stub category',
                'link' => null
        ];
    }

    /**
     * If keywords is set as a search request parameter use it. Otherwise predefined value.
     *
     * @param array $data
     *
     * @return array
     */
    private function getKeywords(array $data): array
    {
        return isset($data['words']) ? [ 0 => ['name' => $data['words']]] : [ 0 => ['name' => 'stub']];
    }
}
