<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TestModuleAdobeStockClient\Model\Client;

/**
 * Represents the raw stock data response from the Adobe Stock service gotten with the Adobe Stock PHP SDK
 */
class RawStockResponse
{
    /**
     * Adobe Stock service raw response
     *
     * @return array
     */
    public function getRawAdobeStockResponse(): array
    {
        return [
            'nb_results' => 347,
            'files' => [
                0 => [
                    'id' => 125463469,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/125463469/2',
                    'thumbnail_240_url' => 'https://t3.ftcdn.net/jpg/01/25/46/34/240_F_125463469_BKIfSBqM9MM7T4PYzUk61zbeM0Uf8KwA.jpg',
                    'width' => 5098,
                    'height' => 3398,
                    'thumbnail_500_url' => 'https://as1.ftcdn.net/jpg/01/25/46/34/500_F_125463469_BKIfSBqM9MM7T4PYzUk61zbeM0Uf8KwA.jpg',
                    'title' => 'Winter knitting by the window',
                    'creator_id' => 200445400,
                    'creator_name' => 'Alena Ozerova',
                    'creation_date' => '2016-11-01 11:11:45.23323',
                    'country_name' => 'Russian Federation',
                    'category' => [
                        'id' => 552,
                        'name' => 'Relaxing',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'chunky',
                        ],
                        1 => [
                            'name' => 'knit up',
                        ],
                        2 => [
                            'name' => 'sweater',
                        ],
                        3 => [
                            'name' => 'winter',
                        ],
                        4 => [
                            'name' => 'closeup',
                        ],
                        5 => [
                            'name' => 'clothing',
                        ],
                        6 => [
                            'name' => 'cold',
                        ],
                        7 => [
                            'name' => 'warm',
                        ],
                        8 => [
                            'name' => 'window',
                        ],
                        9 => [
                            'name' => 'sill',
                        ],
                        10 => [
                            'name' => 'woolen',
                        ],
                        11 => [
                            'name' => 'knit up',
                        ],
                        12 => [
                            'name' => 'threaded',
                        ],
                        13 => [
                            'name' => 'big',
                        ],
                        14 => [
                            'name' => 'thick',
                        ],
                        15 => [
                            'name' => 'nobody',
                        ],
                        16 => [
                            'name' => 'autumn',
                        ],
                        17 => [
                            'name' => 'calm',
                        ],
                        18 => [
                            'name' => 'hot drink',
                        ],
                        19 => [
                            'name' => 'life',
                        ],
                        20 => [
                            'name' => 'breakfast',
                        ],
                        21 => [
                            'name' => 'cosy',
                        ],
                        22 => [
                            'name' => 'cocoa',
                        ],
                        23 => [
                            'name' => 'wooden',
                        ],
                        24 => [
                            'name' => 'still',
                        ],
                        25 => [
                            'name' => 'fall',
                        ],
                        26 => [
                            'name' => 'weather',
                        ],
                        27 => [
                            'name' => 'home',
                        ],
                        28 => [
                            'name' => 'style',
                        ],
                        29 => [
                            'name' => 'comfort',
                        ],
                        30 => [
                            'name' => 'nordic',
                        ],
                        31 => [
                            'name' => 'hot',
                        ],
                        32 => [
                            'name' => 'drink',
                        ],
                        33 => [
                            'name' => 'cup',
                        ],
                        34 => [
                            'name' => 'relaxing',
                        ],
                        35 => [
                            'name' => 'mug',
                        ],
                        36 => [
                            'name' => 'knitted',
                        ],
                        37 => [
                            'name' => 'weekend',
                        ],
                        38 => [
                            'name' => 'interior',
                        ],
                        39 => [
                            'name' => 'mood',
                        ],
                        40 => [
                            'name' => 'lifestyle',
                        ],
                        41 => [
                            'name' => 'comfortable',
                        ],
                        42 => [
                            'name' => 'craft',
                        ],
                        43 => [
                            'name' => 'wool',
                        ],
                        44 => [
                            'name' => 'soft',
                        ],
                        45 => [
                            'name' => 'needle',
                        ],
                        46 => [
                            'name' => 'rest',
                        ],
                        47 => [
                            'name' => 'window sill',
                        ],
                        48 => [
                            'name' => 'top',
                        ],
                        49 => [
                            'name' => 'view',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/125463469?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                1 => [
                    'id' => 193210330,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/193210330/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/93/21/03/240_F_193210330_hBFxw0qkTDFTsqwnPVWw8xLuImrvFi9n.jpg',
                    'width' => 4096,
                    'height' => 2160,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/93/21/03/500_F_193210330_hBFxw0qkTDFTsqwnPVWw8xLuImrvFi9n.jpg',
                    'title' => 'Snowboarding Overhead Top Down View of Snowboarder Riding Through Fresh Powder Snow Down Ski Resort or Backcountry Slope - WInter Extreme Sports Background',
                    'creator_id' => 205700795,
                    'creator_name' => 'CascadeCreatives',
                    'creation_date' => '2018-02-21 01:32:47.269814',
                    'country_name' => 'United States of America',
                    'category' => [
                        'id' => 980,
                        'name' => 'Snowboarding',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'drone',
                        ],
                        1 => [
                            'name' => 'snowboarding',
                        ],
                        2 => [
                            'name' => 'aerial',
                        ],
                        3 => [
                            'name' => 'snowboard',
                        ],
                        4 => [
                            'name' => 'snowboarder',
                        ],
                        5 => [
                            'name' => 'extreme sport',
                        ],
                        6 => [
                            'name' => 'winter',
                        ],
                        7 => [
                            'name' => 'sport',
                        ],
                        8 => [
                            'name' => 'person',
                        ],
                        9 => [
                            'name' => 'up high',
                        ],
                        10 => [
                            'name' => 'ski',
                        ],
                        11 => [
                            'name' => 'skiing',
                        ],
                        12 => [
                            'name' => 'slope',
                        ],
                        13 => [
                            'name' => 'mountain',
                        ],
                        14 => [
                            'name' => 'powder snow',
                        ],
                        15 => [
                            'name' => 'birds eye view',
                        ],
                        16 => [
                            'name' => 'boarding',
                        ],
                        17 => [
                            'name' => 'action',
                        ],
                        18 => [
                            'name' => 'energetic',
                        ],
                        19 => [
                            'name' => 'background',
                        ],
                        20 => [
                            'name' => 'white',
                        ],
                        21 => [
                            'name' => 'turn',
                        ],
                        22 => [
                            'name' => 'forest',
                        ],
                        23 => [
                            'name' => 'fun',
                        ],
                        24 => [
                            'name' => 'exciting',
                        ],
                        25 => [
                            'name' => 'snow',
                        ],
                        26 => [
                            'name' => 'sky',
                        ],
                        27 => [
                            'name' => 'powder',
                        ],
                        28 => [
                            'name' => 'sun',
                        ],
                        29 => [
                            'name' => 'sport',
                        ],
                        30 => [
                            'name' => 'man',
                        ],
                        31 => [
                            'name' => 'extreme',
                        ],
                        32 => [
                            'name' => 'board',
                        ],
                        33 => [
                            'name' => 'rider',
                        ],
                        34 => [
                            'name' => 'adrenalin',
                        ],
                        35 => [
                            'name' => 'guy',
                        ],
                        36 => [
                            'name' => 'freedom',
                        ],
                        37 => [
                            'name' => 'active',
                        ],
                        38 => [
                            'name' => 'activity',
                        ],
                        39 => [
                            'name' => 'landscape',
                        ],
                        40 => [
                            'name' => 'nature',
                        ],
                        41 => [
                            'name' => 'speed',
                        ],
                        42 => [
                            'name' => 'freeride',
                        ],
                        43 => [
                            'name' => 'ride',
                        ],
                        44 => [
                            'name' => 'freestyle',
                        ],
                        45 => [
                            'name' => 'lifestyle',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/193210330?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                2 => [
                    'id' => 145284145,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/145284145/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/45/28/41/240_F_145284145_BC57vx0sNy9juCXzfHqDpMqYq6MUPCPQ.jpg',
                    'width' => 3960,
                    'height' => 2640,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/45/28/41/500_F_145284145_BC57vx0sNy9juCXzfHqDpMqYq6MUPCPQ.jpg',
                    'title' => 'Winter warm scarf around the neck of the girl',
                    'creator_id' => 206917310,
                    'creator_name' => 'Alexey',
                    'creation_date' => '2017-04-21 22:14:41.345175',
                    'country_name' => 'Russian Federation',
                    'category' => [
                        'id' => 643,
                        'name' => 'Lifestyle',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'foulard',
                        ],
                        1 => [
                            'name' => 'winter',
                        ],
                        2 => [
                            'name' => 'wool',
                        ],
                        3 => [
                            'name' => 'girl',
                        ],
                        4 => [
                            'name' => 'clothing',
                        ],
                        5 => [
                            'name' => 'cold',
                        ],
                        6 => [
                            'name' => 'fashion',
                        ],
                        7 => [
                            'name' => 'warmth',
                        ],
                        8 => [
                            'name' => 'woman',
                        ],
                        9 => [
                            'name' => 'young',
                        ],
                        10 => [
                            'name' => 'wrapped',
                        ],
                        11 => [
                            'name' => 'attractive',
                        ],
                        12 => [
                            'name' => 'climate',
                        ],
                        13 => [
                            'name' => 'cuddling',
                        ],
                        14 => [
                            'name' => 'fashionable',
                        ],
                        15 => [
                            'name' => 'female',
                        ],
                        16 => [
                            'name' => 'happy',
                        ],
                        17 => [
                            'name' => 'knitted',
                        ],
                        18 => [
                            'name' => 'lifestyle',
                        ],
                        19 => [
                            'name' => 'neck',
                        ],
                        20 => [
                            'name' => 'person',
                        ],
                        21 => [
                            'name' => 'season',
                        ],
                        22 => [
                            'name' => 'smile',
                        ],
                        23 => [
                            'name' => 'snuggling',
                        ],
                        24 => [
                            'name' => 'square',
                        ],
                        25 => [
                            'name' => 'temperature',
                        ],
                        26 => [
                            'name' => 'trendy',
                        ],
                        27 => [
                            'name' => 'wintery',
                        ],
                        28 => [
                            'name' => 'woolly',
                        ],
                        29 => [
                            'name' => 'sweater',
                        ],
                        30 => [
                            'name' => 'warm',
                        ],
                        31 => [
                            'name' => 'knit up',
                        ],
                        32 => [
                            'name' => 'background',
                        ],
                        33 => [
                            'name' => 'closeup',
                        ],
                        34 => [
                            'name' => 'detail',
                        ],
                        35 => [
                            'name' => 'fabric',
                        ],
                        36 => [
                            'name' => 'soft',
                        ],
                        37 => [
                            'name' => 'abstract',
                        ],
                        38 => [
                            'name' => 'art',
                        ],
                        39 => [
                            'name' => 'blanket',
                        ],
                        40 => [
                            'name' => 'bright',
                        ],
                        41 => [
                            'name' => 'cable',
                        ],
                        42 => [
                            'name' => 'closeup',
                        ],
                        43 => [
                            'name' => 'shop',
                        ],
                        44 => [
                            'name' => 'catalog',
                        ],
                        45 => [
                            'name' => 'strap',
                        ],
                        46 => [
                            'name' => 'leather',
                        ],
                        47 => [
                            'name' => 'grey',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/145284145?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                3 => [
                    'id' => 81214721,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/81214721/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/00/81/21/47/240_F_81214721_xOiCCpEmKNPoLSPXo3UwLP7i8HPiLM78.jpg',
                    'width' => 4928,
                    'height' => 3264,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/00/81/21/47/500_F_81214721_xOiCCpEmKNPoLSPXo3UwLP7i8HPiLM78.jpg',
                    'title' => 'Corrindor inside the Rhone Glacier, Switzerland',
                    'creator_id' => 205505239,
                    'creator_name' => 'szymanskim',
                    'creation_date' => '2015-04-08 09:57:53.244491',
                    'country_name' => 'Switzerland',
                    'category' => [
                        'id' => 609,
                        'name' => 'Other',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'cold',
                        ],
                        1 => [
                            'name' => 'freezing',
                        ],
                        2 => [
                            'name' => 'icy',
                        ],
                        3 => [
                            'name' => 'mysterious',
                        ],
                        4 => [
                            'name' => 'slippery',
                        ],
                        5 => [
                            'name' => 'corridor',
                        ],
                        6 => [
                            'name' => 'alps',
                        ],
                        7 => [
                            'name' => 'winter',
                        ],
                        8 => [
                            'name' => 'swiss',
                        ],
                        9 => [
                            'name' => 'pass',
                        ],
                        10 => [
                            'name' => 'footpath',
                        ],
                        11 => [
                            'name' => 'hole',
                        ],
                        12 => [
                            'name' => 'tunnel',
                        ],
                        13 => [
                            'name' => 'vanish',
                        ],
                        14 => [
                            'name' => 'adventure',
                        ],
                        15 => [
                            'name' => 'cave',
                        ],
                        16 => [
                            'name' => 'natural',
                        ],
                        17 => [
                            'name' => 'switzerland',
                        ],
                        18 => [
                            'name' => 'ice',
                        ],
                        19 => [
                            'name' => 'jungfrau',
                        ],
                        20 => [
                            'name' => 'blue',
                        ],
                        21 => [
                            'name' => 'mountain',
                        ],
                        22 => [
                            'name' => 'exploration',
                        ],
                        23 => [
                            'name' => 'indoor',
                        ],
                        24 => [
                            'name' => 'cavern',
                        ],
                        25 => [
                            'name' => 'point',
                        ],
                        26 => [
                            'name' => 'europa',
                        ],
                        27 => [
                            'name' => 'cave',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/81214721?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                4 => [
                    'id' => 213910590,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/213910590/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/02/13/91/05/240_F_213910590_bQT5UjUHOLWMmCiDm9AAYR6js4QCerR6.jpg',
                    'width' => 3086,
                    'height' => 4628,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/02/13/91/05/500_F_213910590_bQT5UjUHOLWMmCiDm9AAYR6js4QCerR6.jpg',
                    'title' => 'White painted monstera tropical leaf with dripping paint',
                    'creator_id' => 206116816,
                    'creator_name' => 'Zamurovic',
                    'creation_date' => '2018-07-18 09:54:45.057227',
                    'country_name' => 'Serbia',
                    'category' => [
                        'id' => 782,
                        'name' => 'Plants and Flowers',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'leaf',
                        ],
                        1 => [
                            'name' => 'monstera',
                        ],
                        2 => [
                            'name' => 'paint',
                        ],
                        3 => [
                            'name' => 'white',
                        ],
                        4 => [
                            'name' => 'art',
                        ],
                        5 => [
                            'name' => 'plant',
                        ],
                        6 => [
                            'name' => 'dripped',
                        ],
                        7 => [
                            'name' => 'abstract',
                        ],
                        8 => [
                            'name' => 'creative',
                        ],
                        9 => [
                            'name' => 'design',
                        ],
                        10 => [
                            'name' => 'copy space',
                        ],
                        11 => [
                            'name' => 'graphic',
                        ],
                        12 => [
                            'name' => 'no people',
                        ],
                        13 => [
                            'name' => 'studio',
                        ],
                        14 => [
                            'name' => 'nobody',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/213910590?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                5 => [
                    'id' => 180705946,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/180705946/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/80/70/59/240_F_180705946_8bbxWnPUDUuLI2uHqEPqJTD7BHx2BB19.jpg',
                    'width' => 7360,
                    'height' => 4912,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/80/70/59/500_F_180705946_8bbxWnPUDUuLI2uHqEPqJTD7BHx2BB19.jpg',
                    'title' => 'daughter hugging soldier',
                    'creator_id' => 206713618,
                    'creator_name' => 'LIGHTFIELD STUDIOS',
                    'creation_date' => '2017-11-14 11:24:33.332858',
                    'country_name' => 'United States of America',
                    'category' => [
                        'id' => 695,
                        'name' => 'People',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'signs',
                        ],
                        1 => [
                            'name' => 'adult',
                        ],
                        2 => [
                            'name' => 'people',
                        ],
                        3 => [
                            'name' => 'adolescence',
                        ],
                        4 => [
                            'name' => 'children',
                        ],
                        5 => [
                            'name' => 'family',
                        ],
                        6 => [
                            'name' => 'childhood',
                        ],
                        7 => [
                            'name' => 'children',
                        ],
                        8 => [
                            'name' => 'symbol',
                        ],
                        9 => [
                            'name' => 'home',
                        ],
                        10 => [
                            'name' => 'together',
                        ],
                        11 => [
                            'name' => 'togetherness',
                        ],
                        12 => [
                            'name' => 'indoor',
                        ],
                        13 => [
                            'name' => 'us',
                        ],
                        14 => [
                            'name' => 'profession',
                        ],
                        15 => [
                            'name' => 'daughter',
                        ],
                        16 => [
                            'name' => 'hugs',
                        ],
                        17 => [
                            'name' => 'mother',
                        ],
                        18 => [
                            'name' => 'eltern',
                        ],
                        19 => [
                            'name' => 'mother',
                        ],
                        20 => [
                            'name' => 'hug',
                        ],
                        21 => [
                            'name' => 'soldier',
                        ],
                        22 => [
                            'name' => 'camouflage',
                        ],
                        23 => [
                            'name' => 'emblem',
                        ],
                        24 => [
                            'name' => 'patriotism',
                        ],
                        25 => [
                            'name' => 'human relationships',
                        ],
                        26 => [
                            'name' => 'afro',
                        ],
                        27 => [
                            'name' => 'patriot',
                        ],
                        28 => [
                            'name' => 'mother',
                        ],
                        29 => [
                            'name' => 'partial',
                        ],
                        30 => [
                            'name' => 'patriotic',
                        ],
                        31 => [
                            'name' => 'parenthood',
                        ],
                        32 => [
                            'name' => 'professional occupation',
                        ],
                        33 => [
                            'name' => 'black woman',
                        ],
                        34 => [
                            'name' => 'african american',
                        ],
                        35 => [
                            'name' => 'american flag',
                        ],
                        36 => [
                            'name' => 'us',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/180705946?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                6 => [
                    'id' => 126067012,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/126067012/2',
                    'thumbnail_240_url' => 'https://t3.ftcdn.net/jpg/01/26/06/70/240_F_126067012_VDHo0DXpzyUgJLXP5BHzmUvQCgM52q3O.jpg',
                    'width' => 5373,
                    'height' => 3582,
                    'thumbnail_500_url' => 'https://as1.ftcdn.net/jpg/01/26/06/70/500_F_126067012_VDHo0DXpzyUgJLXP5BHzmUvQCgM52q3O.jpg',
                    'title' => 'Christmas Tree Farm',
                    'creator_id' => 206593259,
                    'creator_name' => 'Jayce',
                    'creation_date' => '2016-11-07 08:09:29.523064',
                    'country_name' => 'United States of America',
                    'category' => [
                        'id' => 832,
                        'name' => 'Culture and Religion',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'christmas tree',
                        ],
                        1 => [
                            'name' => 'tree',
                        ],
                        2 => [
                            'name' => 'christmas',
                        ],
                        3 => [
                            'name' => 'farm',
                        ],
                        4 => [
                            'name' => 'landscape',
                        ],
                        5 => [
                            'name' => 'repitition',
                        ],
                        6 => [
                            'name' => 'green',
                        ],
                        7 => [
                            'name' => 'triangle',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/126067012?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                7 => [
                    'id' => 246811446,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/246811446/2',
                    'thumbnail_240_url' => 'https://t3.ftcdn.net/jpg/02/46/81/14/240_F_246811446_iwTs5N49KbtB6jmLcdSeC12X1b1NGh9p.jpg',
                    'width' => 6240,
                    'height' => 4160,
                    'thumbnail_500_url' => 'https://as1.ftcdn.net/jpg/02/46/81/14/500_F_246811446_iwTs5N49KbtB6jmLcdSeC12X1b1NGh9p.jpg',
                    'title' => '【日本の温泉】草津温泉の湯畑',
                    'creator_id' => 207880439,
                    'creator_name' => 'yu_arakawa',
                    'creation_date' => '2019-02-01 14:31:07.543473',
                    'country_name' => 'Japan',
                    'category' => [
                        'id' => 1043,
                        'name' => 'Travel',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'hot spring',
                        ],
                        1 => [
                            'name' => 'source',
                        ],
                        2 => [
                            'name' => 'bathe',
                        ],
                        3 => [
                            'name' => 'vapour',
                        ],
                        4 => [
                            'name' => 'tourist attraction',
                        ],
                        5 => [
                            'name' => 'showplace',
                        ],
                        6 => [
                            'name' => 'snow',
                        ],
                        7 => [
                            'name' => 'winter',
                        ],
                        8 => [
                            'name' => 'japan',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/246811446?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                8 => [
                    'id' => 996327,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/996327/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/00/00/99/63/240_F_996327_MnAK8eJblv567H457lHakPCVAScQak.jpg',
                    'width' => 3888,
                    'height' => 2592,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/00/00/99/63/500_F_996327_MnAK8eJblv567H457lHakPCVAScQak.jpg',
                    'title' => 'ice cold ice',
                    'creator_id' => 5954,
                    'creator_name' => 'flucas',
                    'creation_date' => '2006-07-27 19:13:02',
                    'country_name' => 'Germany',
                    'category' => [
                        'id' => 616,
                        'name' => 'Spring',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'abstract',
                        ],
                        1 => [
                            'name' => 'water',
                        ],
                        2 => [
                            'name' => 'background',
                        ],
                        3 => [
                            'name' => 'blue',
                        ],
                        4 => [
                            'name' => 'brilliance',
                        ],
                        5 => [
                            'name' => 'christmas',
                        ],
                        6 => [
                            'name' => 'close',
                        ],
                        7 => [
                            'name' => 'closeup',
                        ],
                        8 => [
                            'name' => 'cold',
                        ],
                        9 => [
                            'name' => 'colours',
                        ],
                        10 => [
                            'name' => 'cool',
                        ],
                        11 => [
                            'name' => 'crystal',
                        ],
                        12 => [
                            'name' => 'cube',
                        ],
                        13 => [
                            'name' => 'detail',
                        ],
                        14 => [
                            'name' => 'dripped',
                        ],
                        15 => [
                            'name' => 'freeze',
                        ],
                        16 => [
                            'name' => 'fresh',
                        ],
                        17 => [
                            'name' => 'frost',
                        ],
                        18 => [
                            'name' => 'frosty',
                        ],
                        19 => [
                            'name' => 'glacé',
                        ],
                        20 => [
                            'name' => 'glasses',
                        ],
                        21 => [
                            'name' => 'gleam',
                        ],
                        22 => [
                            'name' => 'glimmer',
                        ],
                        23 => [
                            'name' => 'sparking',
                        ],
                        24 => [
                            'name' => 'glistering',
                        ],
                        25 => [
                            'name' => 'ice',
                        ],
                        26 => [
                            'name' => 'icicle',
                        ],
                        27 => [
                            'name' => 'christmas',
                        ],
                        28 => [
                            'name' => 'liquid',
                        ],
                        29 => [
                            'name' => 'macro',
                        ],
                        30 => [
                            'name' => 'melt',
                        ],
                        31 => [
                            'name' => 'natural',
                        ],
                        32 => [
                            'name' => 'season',
                        ],
                        33 => [
                            'name' => 'shimmer',
                        ],
                        34 => [
                            'name' => 'shining',
                        ],
                        35 => [
                            'name' => 'sparkle',
                        ],
                        36 => [
                            'name' => 'spike',
                        ],
                        37 => [
                            'name' => 'spring',
                        ],
                        38 => [
                            'name' => 'structure',
                        ],
                        39 => [
                            'name' => 'surface',
                        ],
                        40 => [
                            'name' => 'texture',
                        ],
                        41 => [
                            'name' => 'transparent',
                        ],
                        42 => [
                            'name' => 'water',
                        ],
                        43 => [
                            'name' => 'winter',
                        ],
                        44 => [
                            'name' => 'snowflake',
                        ],
                        45 => [
                            'name' => 'flower',
                        ],
                        46 => [
                            'name' => 'element',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/996327?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                9 => [
                    'id' => 201953179,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/201953179/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/02/01/95/31/240_F_201953179_LxKXBQSL9wd6L4mKdjKrLl8wx1WlhLtt.jpg',
                    'width' => 6000,
                    'height' => 4006,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/02/01/95/31/500_F_201953179_LxKXBQSL9wd6L4mKdjKrLl8wx1WlhLtt.jpg',
                    'title' => 'Portrait of black woman with dreadlocks hair',
                    'creator_id' => 204567087,
                    'creator_name' => 'Rawpixel.com',
                    'creation_date' => '2018-04-24 02:17:27.82941',
                    'country_name' => 'United Kingdom of Great Britain and Northern Ireland',
                    'category' => [
                        'id' => 695,
                        'name' => 'People',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'adult',
                        ],
                        1 => [
                            'name' => 'african',
                        ],
                        2 => [
                            'name' => 'african american',
                        ],
                        3 => [
                            'name' => 'alone',
                        ],
                        4 => [
                            'name' => 'america',
                        ],
                        5 => [
                            'name' => 'american',
                        ],
                        6 => [
                            'name' => 'author',
                        ],
                        7 => [
                            'name' => 'black',
                        ],
                        8 => [
                            'name' => 'negros',
                        ],
                        9 => [
                            'name' => 'book',
                        ],
                        10 => [
                            'name' => 'cafes',
                        ],
                        11 => [
                            'name' => 'casual attire',
                        ],
                        12 => [
                            'name' => 'diary',
                        ],
                        13 => [
                            'name' => 'dreadlocks',
                        ],
                        14 => [
                            'name' => 'editor',
                        ],
                        15 => [
                            'name' => 'emotion',
                        ],
                        16 => [
                            'name' => 'expression',
                        ],
                        17 => [
                            'name' => 'face',
                        ],
                        18 => [
                            'name' => 'feeling',
                        ],
                        19 => [
                            'name' => 'female',
                        ],
                        20 => [
                            'name' => 'feminine',
                        ],
                        21 => [
                            'name' => 'girl',
                        ],
                        22 => [
                            'name' => 'coiffure',
                        ],
                        23 => [
                            'name' => 'home',
                        ],
                        24 => [
                            'name' => 'house',
                        ],
                        25 => [
                            'name' => 'journal',
                        ],
                        26 => [
                            'name' => 'lifestyle',
                        ],
                        27 => [
                            'name' => '1',
                        ],
                        28 => [
                            'name' => 'person',
                        ],
                        29 => [
                            'name' => 'portrait',
                        ],
                        30 => [
                            'name' => 'sitting',
                        ],
                        31 => [
                            'name' => 'solo',
                        ],
                        32 => [
                            'name' => 'studying',
                        ],
                        33 => [
                            'name' => 'sweater',
                        ],
                        34 => [
                            'name' => 'thinking',
                        ],
                        35 => [
                            'name' => 'thoughtful',
                        ],
                        36 => [
                            'name' => 'winter',
                        ],
                        37 => [
                            'name' => 'woman',
                        ],
                        38 => [
                            'name' => 'working',
                        ],
                        39 => [
                            'name' => 'writer',
                        ],
                        40 => [
                            'name' => 'writing',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/201953179?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                10 => [
                    'id' => 99095345,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/99095345/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/00/99/09/53/240_F_99095345_D355ceiLff0ZwJnWMaDTUwB1zs2Fej1i.jpg',
                    'width' => 3996,
                    'height' => 2664,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/00/99/09/53/500_F_99095345_D355ceiLff0ZwJnWMaDTUwB1zs2Fej1i.jpg',
                    'title' => '鶴',
                    'creator_id' => 202689149,
                    'creator_name' => 'makieni',
                    'creation_date' => '2016-01-01 23:34:14.014846',
                    'country_name' => 'Japan',
                    'category' => [
                        'id' => 3,
                        'name' => 'Birds',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'crane',
                        ],
                        1 => [
                            'name' => 'vine',
                        ],
                        2 => [
                            'name' => 'crane',
                        ],
                        3 => [
                            'name' => 'bird',
                        ],
                        4 => [
                            'name' => 'animal',
                        ],
                        5 => [
                            'name' => 'wild',
                        ],
                        6 => [
                            'name' => 'wildlife',
                        ],
                        7 => [
                            'name' => 'winter',
                        ],
                        8 => [
                            'name' => 'snow',
                        ],
                        9 => [
                            'name' => 'snow',
                        ],
                        10 => [
                            'name' => 'cold',
                        ],
                        11 => [
                            'name' => 'season',
                        ],
                        12 => [
                            'name' => 'four seasons',
                        ],
                        13 => [
                            'name' => 'hokkaido',
                        ],
                        14 => [
                            'name' => 'dancing',
                        ],
                        15 => [
                            'name' => 'dance',
                        ],
                        16 => [
                            'name' => 'courtship',
                        ],
                        17 => [
                            'name' => 'walk',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/99095345?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                11 => [
                    'id' => 265222752,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/265222752/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/02/65/22/27/240_F_265222752_FlbEzWyiqSjY8RiPfitazMOUuNBpLQ1R.jpg',
                    'width' => 5464,
                    'height' => 3640,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/02/65/22/27/500_F_265222752_FlbEzWyiqSjY8RiPfitazMOUuNBpLQ1R.jpg',
                    'title' => 'two athletic man floats on a red boat in river',
                    'creator_id' => 205249513,
                    'creator_name' => 'teksomolika',
                    'creation_date' => '2019-04-30 00:13:30.637753',
                    'country_name' => 'Ukraine',
                    'category' => [
                        'id' => 922,
                        'name' => 'Sports',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'activity',
                        ],
                        1 => [
                            'name' => 'adult',
                        ],
                        2 => [
                            'name' => 'adventure',
                        ],
                        3 => [
                            'name' => 'weather',
                        ],
                        4 => [
                            'name' => 'blue',
                        ],
                        5 => [
                            'name' => 'boat',
                        ],
                        6 => [
                            'name' => 'canoe',
                        ],
                        7 => [
                            'name' => 'coast',
                        ],
                        8 => [
                            'name' => 'costume',
                        ],
                        9 => [
                            'name' => 'day',
                        ],
                        10 => [
                            'name' => 'enjoyment',
                        ],
                        11 => [
                            'name' => 'exercising',
                        ],
                        12 => [
                            'name' => 'exploration',
                        ],
                        13 => [
                            'name' => 'healthy',
                        ],
                        14 => [
                            'name' => 'holiday',
                        ],
                        15 => [
                            'name' => 'ice',
                        ],
                        16 => [
                            'name' => 'floe',
                        ],
                        17 => [
                            'name' => 'jacket',
                        ],
                        18 => [
                            'name' => 'kayak',
                        ],
                        19 => [
                            'name' => 'kayak',
                        ],
                        20 => [
                            'name' => 'lake',
                        ],
                        21 => [
                            'name' => 'lifestyle',
                        ],
                        22 => [
                            'name' => 'male',
                        ],
                        23 => [
                            'name' => 'man',
                        ],
                        24 => [
                            'name' => 'nautical',
                        ],
                        25 => [
                            'name' => 'oar',
                        ],
                        26 => [
                            'name' => 'outdoors',
                        ],
                        27 => [
                            'name' => 'paddle',
                        ],
                        28 => [
                            'name' => 'person',
                        ],
                        29 => [
                            'name' => 'duck pond',
                        ],
                        30 => [
                            'name' => 'red',
                        ],
                        31 => [
                            'name' => 'relaxation',
                        ],
                        32 => [
                            'name' => 'resting',
                        ],
                        33 => [
                            'name' => 'river',
                        ],
                        34 => [
                            'name' => 'seasonal',
                        ],
                        35 => [
                            'name' => 'sitting',
                        ],
                        36 => [
                            'name' => 'sport',
                        ],
                        37 => [
                            'name' => 'spring',
                        ],
                        38 => [
                            'name' => 'sunny',
                        ],
                        39 => [
                            'name' => 'tour tourism',
                        ],
                        40 => [
                            'name' => 'travel',
                        ],
                        41 => [
                            'name' => 'vacation',
                        ],
                        42 => [
                            'name' => 'vest',
                        ],
                        43 => [
                            'name' => 'water',
                        ],
                        44 => [
                            'name' => 'docked',
                        ],
                        45 => [
                            'name' => 'winter',
                        ],
                        46 => [
                            'name' => 'reed',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/265222752?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                12 => [
                    'id' => 139205108,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/139205108/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/39/20/51/240_F_139205108_XAQcMphYUksGE73JVMcEgRHNwA3BBCVZ.jpg',
                    'width' => 4256,
                    'height' => 2832,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/39/20/51/500_F_139205108_XAQcMphYUksGE73JVMcEgRHNwA3BBCVZ.jpg',
                    'title' => 'White bear portrait close up isolated on black background',
                    'creator_id' => 206455079,
                    'creator_name' => 'kwadrat70',
                    'creation_date' => '2017-03-03 11:13:42.22074',
                    'country_name' => 'Ukraine',
                    'category' => [
                        'id' => 64,
                        'name' => 'Polar Bears',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'bear',
                        ],
                        1 => [
                            'name' => 'white',
                        ],
                        2 => [
                            'name' => 'polar',
                        ],
                        3 => [
                            'name' => 'wildlife',
                        ],
                        4 => [
                            'name' => 'wild',
                        ],
                        5 => [
                            'name' => 'mammal',
                        ],
                        6 => [
                            'name' => 'arctic',
                        ],
                        7 => [
                            'name' => 'nature',
                        ],
                        8 => [
                            'name' => 'portrait',
                        ],
                        9 => [
                            'name' => 'animal',
                        ],
                        10 => [
                            'name' => 'outdoors',
                        ],
                        11 => [
                            'name' => 'winter',
                        ],
                        12 => [
                            'name' => 'fur',
                        ],
                        13 => [
                            'name' => 'north',
                        ],
                        14 => [
                            'name' => 'carnivore',
                        ],
                        15 => [
                            'name' => 'background',
                        ],
                        16 => [
                            'name' => 'cold',
                        ],
                        17 => [
                            'name' => 'male',
                        ],
                        18 => [
                            'name' => 'baby animal',
                        ],
                        19 => [
                            'name' => 'black',
                        ],
                        20 => [
                            'name' => 'adult',
                        ],
                        21 => [
                            'name' => 'closeup',
                        ],
                        22 => [
                            'name' => 'cute',
                        ],
                        23 => [
                            'name' => 'head',
                        ],
                        24 => [
                            'name' => 'power',
                        ],
                        25 => [
                            'name' => 'paw',
                        ],
                        26 => [
                            'name' => 'animal',
                        ],
                        27 => [
                            'name' => 'big',
                        ],
                        28 => [
                            'name' => 'strength',
                        ],
                        29 => [
                            'name' => 'predator',
                        ],
                        30 => [
                            'name' => 'hunter',
                        ],
                        31 => [
                            'name' => 'isolated',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/139205108?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                13 => [
                    'id' => 193601733,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/193601733/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/93/60/17/240_F_193601733_Kyppgfs8JMn1JTiYgtaMoGTTWrxW0LZB.jpg',
                    'width' => 5676,
                    'height' => 3376,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/93/60/17/500_F_193601733_Kyppgfs8JMn1JTiYgtaMoGTTWrxW0LZB.jpg',
                    'title' => 'hair flowing in the wind, a woman and a cold day',
                    'creator_id' => 201407413,
                    'creator_name' => 'Tatiana Zaghet',
                    'creation_date' => '2018-02-23 18:55:06.215495',
                    'country_name' => 'Italy',
                    'category' => [
                        'id' => 709,
                        'name' => 'Hair',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'woman',
                        ],
                        1 => [
                            'name' => 'lady',
                        ],
                        2 => [
                            'name' => 'wind',
                        ],
                        3 => [
                            'name' => 'windy',
                        ],
                        4 => [
                            'name' => 'hair',
                        ],
                        5 => [
                            'name' => 'cold',
                        ],
                        6 => [
                            'name' => 'winter',
                        ],
                        7 => [
                            'name' => 'day',
                        ],
                        8 => [
                            'name' => 'meditation',
                        ],
                        9 => [
                            'name' => 'stress',
                        ],
                        10 => [
                            'name' => 'long hair',
                        ],
                        11 => [
                            'name' => 'brown',
                        ],
                    ],
                    'media_type_id' => 5,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/193601733?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                14 => [
                    'id' => 230607584,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/230607584/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/02/30/60/75/240_F_230607584_ctoB3XyGIIspoO1vzhp3c3F5hAcjGqhj.jpg',
                    'width' => 5275,
                    'height' => 3432,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/02/30/60/75/500_F_230607584_ctoB3XyGIIspoO1vzhp3c3F5hAcjGqhj.jpg',
                    'title' => 'Man hand holding wooden honey dipper, honey spoon on top of glass of tea/ medicine and dripping honey in hot tea. Knitted socks, small jar of honey, garlic on wooden table against black background.',
                    'creator_id' => 207688781,
                    'creator_name' => 'FotoHelin',
                    'creation_date' => '2018-10-30 12:30:40.379438',
                    'country_name' => 'Estonia',
                    'category' => [
                        'id' => 214,
                        'name' => 'Drinks',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'folk',
                        ],
                        1 => [
                            'name' => 'remedy',
                        ],
                        2 => [
                            'name' => 'garlic',
                        ],
                        3 => [
                            'name' => 'tea',
                        ],
                        4 => [
                            'name' => 'traditional',
                        ],
                        5 => [
                            'name' => 'fever',
                        ],
                        6 => [
                            'name' => 'flu',
                        ],
                        7 => [
                            'name' => 'cold',
                        ],
                        8 => [
                            'name' => 'medicals',
                        ],
                        9 => [
                            'name' => 'pouring',
                        ],
                        10 => [
                            'name' => 'herbal',
                        ],
                        11 => [
                            'name' => 'honey',
                        ],
                        12 => [
                            'name' => 'background',
                        ],
                        13 => [
                            'name' => 'studio',
                        ],
                        14 => [
                            'name' => 'treatment',
                        ],
                        15 => [
                            'name' => 'remedy',
                        ],
                        16 => [
                            'name' => 'wooden',
                        ],
                        17 => [
                            'name' => 'health',
                        ],
                        18 => [
                            'name' => 'mineral',
                        ],
                        19 => [
                            'name' => 'dripped',
                        ],
                        20 => [
                            'name' => 'spoon',
                        ],
                        21 => [
                            'name' => 'hold',
                        ],
                        22 => [
                            'name' => 'hand',
                        ],
                        23 => [
                            'name' => 'man',
                        ],
                        24 => [
                            'name' => 'sore throat',
                        ],
                        25 => [
                            'name' => 'cough',
                        ],
                        26 => [
                            'name' => 'healthy',
                        ],
                        27 => [
                            'name' => 'antibacterial',
                        ],
                        28 => [
                            'name' => 'concept',
                        ],
                        29 => [
                            'name' => 'warming',
                        ],
                        30 => [
                            'name' => 'cup',
                        ],
                        31 => [
                            'name' => 'transparent',
                        ],
                        32 => [
                            'name' => 'glasses',
                        ],
                        33 => [
                            'name' => 'jar',
                        ],
                        34 => [
                            'name' => 'wooden table',
                        ],
                        35 => [
                            'name' => 'set',
                        ],
                        36 => [
                            'name' => 'black',
                        ],
                        37 => [
                            'name' => 'hot',
                        ],
                        38 => [
                            'name' => 'natural',
                        ],
                        39 => [
                            'name' => 'sick',
                        ],
                        40 => [
                            'name' => 'ill',
                        ],
                        41 => [
                            'name' => 'disease',
                        ],
                        42 => [
                            'name' => 'medicative',
                        ],
                        43 => [
                            'name' => 'healing',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/230607584?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                15 => [
                    'id' => 168034758,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/168034758/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/68/03/47/240_F_168034758_99ignarFY2WWhoeMfrVtrkngRvOHd6yc.jpg',
                    'width' => 5792,
                    'height' => 8688,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/68/03/47/500_F_168034758_99ignarFY2WWhoeMfrVtrkngRvOHd6yc.jpg',
                    'title' => 'African businessman standing at start up',
                    'creator_id' => 224608,
                    'creator_name' => 'Jacob Lund ',
                    'creation_date' => '2017-08-16 11:26:48.512793',
                    'country_name' => 'Denmark',
                    'category' => [
                        'id' => 191,
                        'name' => 'Office Life',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'male',
                        ],
                        1 => [
                            'name' => 'portrait',
                        ],
                        2 => [
                            'name' => 'office',
                        ],
                        3 => [
                            'name' => 'startup',
                        ],
                        4 => [
                            'name' => 'business',
                        ],
                        5 => [
                            'name' => 'african',
                        ],
                        6 => [
                            'name' => 'background',
                        ],
                        7 => [
                            'name' => 'black',
                        ],
                        8 => [
                            'name' => 'blank',
                        ],
                        9 => [
                            'name' => 'casual attire',
                        ],
                        10 => [
                            'name' => 'co-worker',
                        ],
                        11 => [
                            'name' => 'creative',
                        ],
                        12 => [
                            'name' => 'design',
                        ],
                        13 => [
                            'name' => 'designer',
                        ],
                        14 => [
                            'name' => 'executive',
                        ],
                        15 => [
                            'name' => 'expression',
                        ],
                        16 => [
                            'name' => 'photogenic',
                        ],
                        17 => [
                            'name' => 'looking',
                        ],
                        18 => [
                            'name' => 'man',
                        ],
                        19 => [
                            'name' => 'people',
                        ],
                        20 => [
                            'name' => 'professional',
                        ],
                        21 => [
                            'name' => 'real',
                        ],
                        22 => [
                            'name' => 'standing',
                        ],
                        23 => [
                            'name' => 'staring',
                        ],
                        24 => [
                            'name' => 'vertical',
                        ],
                        25 => [
                            'name' => 'working',
                        ],
                        26 => [
                            'name' => 'young',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/168034758?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                16 => [
                    'id' => 208330713,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/208330713/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/02/08/33/07/240_F_208330713_OnmjJmlMHjtCrHVv1cLzzXg8tNXcStQg.jpg',
                    'width' => 5473,
                    'height' => 3649,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/02/08/33/07/500_F_208330713_OnmjJmlMHjtCrHVv1cLzzXg8tNXcStQg.jpg',
                    'title' => 'Hygge concept with cat, book and coffee in the bed',
                    'creator_id' => 200445400,
                    'creator_name' => 'Alena Ozerova',
                    'creation_date' => '2018-06-08 06:00:19.586287',
                    'country_name' => 'Russian Federation',
                    'category' => [
                        'id' => 552,
                        'name' => 'Relaxing',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'cosy',
                        ],
                        1 => [
                            'name' => 'autumn',
                        ],
                        2 => [
                            'name' => 'home',
                        ],
                        3 => [
                            'name' => 'fall',
                        ],
                        4 => [
                            'name' => 'cute',
                        ],
                        5 => [
                            'name' => 'cat',
                        ],
                        6 => [
                            'name' => 'kitten',
                        ],
                        7 => [
                            'name' => 'blanket',
                        ],
                        8 => [
                            'name' => 'weekend',
                        ],
                        9 => [
                            'name' => 'winter',
                        ],
                        10 => [
                            'name' => 'pet',
                        ],
                        11 => [
                            'name' => 'lazy',
                        ],
                        12 => [
                            'name' => 'morning',
                        ],
                        13 => [
                            'name' => 'hot drink',
                        ],
                        14 => [
                            'name' => 'bed',
                        ],
                        15 => [
                            'name' => 'sleep',
                        ],
                        16 => [
                            'name' => 'warm',
                        ],
                        17 => [
                            'name' => 'house',
                        ],
                        18 => [
                            'name' => 'room',
                        ],
                        19 => [
                            'name' => 'soft',
                        ],
                        20 => [
                            'name' => 'breakfast',
                        ],
                        21 => [
                            'name' => 'relax',
                        ],
                        22 => [
                            'name' => 'cold',
                        ],
                        23 => [
                            'name' => 'rest',
                        ],
                        24 => [
                            'name' => 'top',
                        ],
                        25 => [
                            'name' => 'lifestyle',
                        ],
                        26 => [
                            'name' => 'bedding',
                        ],
                        27 => [
                            'name' => 'concept',
                        ],
                        28 => [
                            'name' => 'nordic',
                        ],
                        29 => [
                            'name' => 'book',
                        ],
                        30 => [
                            'name' => 'cosy',
                        ],
                        31 => [
                            'name' => 'view',
                        ],
                        32 => [
                            'name' => 'read',
                        ],
                        33 => [
                            'name' => 'reading',
                        ],
                        34 => [
                            'name' => 'drink',
                        ],
                        35 => [
                            'name' => 'pillow',
                        ],
                        36 => [
                            'name' => 'cushion',
                        ],
                        37 => [
                            'name' => 'tea',
                        ],
                        38 => [
                            'name' => 'life',
                        ],
                        39 => [
                            'name' => 'still',
                        ],
                        40 => [
                            'name' => 'comfy',
                        ],
                        41 => [
                            'name' => 'sleepy',
                        ],
                        42 => [
                            'name' => 'dream',
                        ],
                        43 => [
                            'name' => 'ginger',
                        ],
                        44 => [
                            'name' => 'owner',
                        ],
                        45 => [
                            'name' => 'love',
                        ],
                        46 => [
                            'name' => 'hand',
                        ],
                        47 => [
                            'name' => 'person',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/208330713?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                17 => [
                    'id' => 74443764,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/74443764/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/00/74/44/37/240_F_74443764_8Ghf6q8zQIwgbbOctbkVT0t3nl40xg3v.jpg',
                    'width' => 3840,
                    'height' => 4351,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/00/74/44/37/500_F_74443764_8Ghf6q8zQIwgbbOctbkVT0t3nl40xg3v.jpg',
                    'title' => 'beautiful little girl with gifts on a windowsill',
                    'creator_id' => 203979331,
                    'creator_name' => 'anikanes',
                    'creation_date' => '2014-12-09 18:46:24.75605',
                    'country_name' => 'Ukraine',
                    'category' => [
                        'id' => 834,
                        'name' => 'Christmas',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'baby',
                        ],
                        1 => [
                            'name' => 'beautiful',
                        ],
                        2 => [
                            'name' => 'caucasian',
                        ],
                        3 => [
                            'name' => 'celebration',
                        ],
                        4 => [
                            'name' => 'chair',
                        ],
                        5 => [
                            'name' => 'children',
                        ],
                        6 => [
                            'name' => 'childhood',
                        ],
                        7 => [
                            'name' => 'christmas',
                        ],
                        8 => [
                            'name' => 'curly',
                        ],
                        9 => [
                            'name' => 'cute',
                        ],
                        10 => [
                            'name' => 'december',
                        ],
                        11 => [
                            'name' => 'decorating',
                        ],
                        12 => [
                            'name' => 'decoration',
                        ],
                        13 => [
                            'name' => 'door',
                        ],
                        14 => [
                            'name' => 'dress',
                        ],
                        15 => [
                            'name' => 'family',
                        ],
                        16 => [
                            'name' => 'floor',
                        ],
                        17 => [
                            'name' => 'garden',
                        ],
                        18 => [
                            'name' => 'gift',
                        ],
                        19 => [
                            'name' => 'girl',
                        ],
                        20 => [
                            'name' => 'happy',
                        ],
                        21 => [
                            'name' => 'holiday',
                        ],
                        22 => [
                            'name' => 'home',
                        ],
                        23 => [
                            'name' => 'interior',
                        ],
                        24 => [
                            'name' => 'joy',
                        ],
                        25 => [
                            'name' => 'children',
                        ],
                        26 => [
                            'name' => 'little',
                        ],
                        27 => [
                            'name' => 'living',
                        ],
                        28 => [
                            'name' => 'necklace',
                        ],
                        29 => [
                            'name' => 'new',
                        ],
                        30 => [
                            'name' => 'opening',
                        ],
                        31 => [
                            'name' => 'party',
                        ],
                        32 => [
                            'name' => 'pearl',
                        ],
                        33 => [
                            'name' => 'person',
                        ],
                        34 => [
                            'name' => 'portrait',
                        ],
                        35 => [
                            'name' => 'present',
                        ],
                        36 => [
                            'name' => 'red',
                        ],
                        37 => [
                            'name' => 'rocking',
                        ],
                        38 => [
                            'name' => 'room',
                        ],
                        39 => [
                            'name' => 'santa',
                        ],
                        40 => [
                            'name' => 'season',
                        ],
                        41 => [
                            'name' => 'sitting',
                        ],
                        42 => [
                            'name' => 'snow',
                        ],
                        43 => [
                            'name' => 'toddler',
                        ],
                        44 => [
                            'name' => 'tree',
                        ],
                        45 => [
                            'name' => 'white',
                        ],
                        46 => [
                            'name' => 'window',
                        ],
                        47 => [
                            'name' => 'winter',
                        ],
                        48 => [
                            'name' => 'christmas',
                        ],
                        49 => [
                            'name' => 'year',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/74443764?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                18 => [
                    'id' => 232892201,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/232892201/2',
                    'thumbnail_240_url' => 'https://t3.ftcdn.net/jpg/02/32/89/22/240_F_232892201_uSHFgfpRNUv1w2mnYNJzWpIKYgwyu2yf.jpg',
                    'width' => 7000,
                    'height' => 4853,
                    'thumbnail_500_url' => 'https://as1.ftcdn.net/jpg/02/32/89/22/500_F_232892201_uSHFgfpRNUv1w2mnYNJzWpIKYgwyu2yf.jpg',
                    'title' => 'Happy grandmother hugging her grandson',
                    'creator_id' => 204567087,
                    'creator_name' => 'Rawpixel.com',
                    'creation_date' => '2018-11-12 01:50:27.088508',
                    'country_name' => 'United States of America',
                    'category' => [
                        'id' => 695,
                        'name' => 'People',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'bonding',
                        ],
                        1 => [
                            'name' => 'boy',
                        ],
                        2 => [
                            'name' => 'british',
                        ],
                        3 => [
                            'name' => 'care',
                        ],
                        4 => [
                            'name' => 'children',
                        ],
                        5 => [
                            'name' => 'closeup',
                        ],
                        6 => [
                            'name' => 'closeness',
                        ],
                        7 => [
                            'name' => 'closeup',
                        ],
                        8 => [
                            'name' => 'cute',
                        ],
                        9 => [
                            'name' => 'two together',
                        ],
                        10 => [
                            'name' => 'embracing',
                        ],
                        11 => [
                            'name' => 'endearing',
                        ],
                        12 => [
                            'name' => 'england',
                        ],
                        13 => [
                            'name' => 'english',
                        ],
                        14 => [
                            'name' => 'eyeglass',
                        ],
                        15 => [
                            'name' => 'family',
                        ],
                        16 => [
                            'name' => 'grandmother',
                        ],
                        17 => [
                            'name' => 'grandparent',
                        ],
                        18 => [
                            'name' => 'grandson',
                        ],
                        19 => [
                            'name' => 'grandmother',
                        ],
                        20 => [
                            'name' => 'happiness',
                        ],
                        21 => [
                            'name' => 'happy',
                        ],
                        22 => [
                            'name' => 'hug',
                        ],
                        23 => [
                            'name' => 'hugging',
                        ],
                        24 => [
                            'name' => 'joy',
                        ],
                        25 => [
                            'name' => 'children',
                        ],
                        26 => [
                            'name' => 'leisure',
                        ],
                        27 => [
                            'name' => 'love',
                        ],
                        28 => [
                            'name' => 'loving',
                        ],
                        29 => [
                            'name' => 'nature',
                        ],
                        30 => [
                            'name' => 'old',
                        ],
                        31 => [
                            'name' => 'outside',
                        ],
                        32 => [
                            'name' => 'quality time',
                        ],
                        33 => [
                            'name' => 'retired',
                        ],
                        34 => [
                            'name' => 'retirement',
                        ],
                        35 => [
                            'name' => 'season',
                        ],
                        36 => [
                            'name' => 'senior',
                        ],
                        37 => [
                            'name' => 'smile',
                        ],
                        38 => [
                            'name' => 'smiling',
                        ],
                        39 => [
                            'name' => 'sweet',
                        ],
                        40 => [
                            'name' => 'britain',
                        ],
                        41 => [
                            'name' => 'vacation',
                        ],
                        42 => [
                            'name' => 'wales',
                        ],
                        43 => [
                            'name' => 'weather',
                        ],
                        44 => [
                            'name' => 'western',
                        ],
                        45 => [
                            'name' => 'white hair',
                        ],
                        46 => [
                            'name' => 'winter',
                        ],
                        47 => [
                            'name' => 'young',
                        ],
                        48 => [
                            'name' => 'youth',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/232892201?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
                19 => [
                    'id' => 194424195,
                    'comp_url' => 'https://stock.adobe.com/Rest/Libraries/Watermarked/Download/194424195/2',
                    'thumbnail_240_url' => 'https://t4.ftcdn.net/jpg/01/94/42/41/240_F_194424195_ymCmACmKlV0wj6iYLey9XStod3VUpCye.jpg',
                    'width' => 4608,
                    'height' => 3282,
                    'thumbnail_500_url' => 'https://as2.ftcdn.net/jpg/01/94/42/41/500_F_194424195_ymCmACmKlV0wj6iYLey9XStod3VUpCye.jpg',
                    'title' => 'Snowscape texture',
                    'creator_id' => 223075,
                    'creator_name' => 'Ralph Musto',
                    'creation_date' => '2018-03-01 13:23:56.237836',
                    'country_name' => 'United Kingdom of Great Britain and Northern Ireland',
                    'category' => [
                        'id' => 596,
                        'name' => 'Landscapes',
                    ],
                    'keywords' => [
                        0 => [
                            'name' => 'winter',
                        ],
                        1 => [
                            'name' => 'snow',
                        ],
                        2 => [
                            'name' => 'ice',
                        ],
                        3 => [
                            'name' => 'snowscape',
                        ],
                        4 => [
                            'name' => 'cold',
                        ],
                        5 => [
                            'name' => 'white',
                        ],
                        6 => [
                            'name' => 'snowy',
                        ],
                        7 => [
                            'name' => 'flake',
                        ],
                        8 => [
                            'name' => 'background',
                        ],
                        9 => [
                            'name' => 'nobody',
                        ],
                        10 => [
                            'name' => 'texture',
                        ],
                        11 => [
                            'name' => 'pattern',
                        ],
                    ],
                    'media_type_id' => 1,
                    'content_type' => 'image/jpeg',
                    'details_url' => 'https://stock.adobe.com/194424195?as_channel=affiliate&as_source=api&as_content=7f387d439e4f4e64ae42060fc571c456',
                    'premium_level_id' => 0,
                ],
            ],
        ];
    }
}

