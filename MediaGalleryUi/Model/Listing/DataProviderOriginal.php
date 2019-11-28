<?php

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;

class DataProviderOriginal extends UiComponentDataProvider
{
     public function __construct(
         $name,
         $primaryFieldName,
         $requestFieldName,
         ReportingInterface $reporting,
         SearchCriteriaBuilder $searchCriteriaBuilder,
         RequestInterface $request,
         FilterBuilder $filterBuilder,
         array $meta = [],
         array $data = []
     ) {
         parent::__construct(
             $name,
             $primaryFieldName,
             $requestFieldName,
             $reporting,
             $searchCriteriaBuilder,
             $request,
             $filterBuilder,
             $meta,
             $data
         );
     }

    private function getErrorData(): array
    {
        return [
            'items' => [],
            'totalRecords' => 0,
            'errorMessage' => 'Error'
        ];
    }

     public function getData(): array
     {
         return $this->getSampleData();
     }

    private function getSampleData(): array
    {
        return array(
            'items' =>
                array(
                    0 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 125463469,
                            'title' => 'Winter knitting by the window',
                            'url' => 'https://t3.ftcdn.net/jpg/01/25/46/34/240_F_125463469_BKIfSBqM9MM7T4PYzUk61zbeM0Uf8KwA.jpg',
                            'preview_url' => 'https://as1.ftcdn.net/jpg/01/25/46/34/500_F_125463469_BKIfSBqM9MM7T4PYzUk61zbeM0Uf8KwA.jpg',
                            'width' => 5098,
                            'height' => 3398,
                            'overlay' => 'Special'
                        ),
                    1 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 193210330,
                            'title' => 'Snowboarding Overhead Top Down View of Snowboarder Riding Through Fresh Powder Snow Down Ski Resort or Backcountry Slope - WInter Extreme Sports Background',
                            'url' => 'https://t4.ftcdn.net/jpg/01/93/21/03/240_F_193210330_hBFxw0qkTDFTsqwnPVWw8xLuImrvFi9n.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/93/21/03/500_F_193210330_hBFxw0qkTDFTsqwnPVWw8xLuImrvFi9n.jpg',
                            'width' => 4096,
                            'height' => 2160
                        ),
                    2 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 145284145,
                            'title' => 'Winter warm scarf around the neck of the girl',
                            'url' => 'https://t4.ftcdn.net/jpg/01/45/28/41/240_F_145284145_BC57vx0sNy9juCXzfHqDpMqYq6MUPCPQ.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/45/28/41/500_F_145284145_BC57vx0sNy9juCXzfHqDpMqYq6MUPCPQ.jpg',
                            'width' => 3960,
                            'height' => 2640
                        ),
                    3 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 81214721,
                            'title' => 'Corrindor inside the Rhone Glacier, Switzerland',
                            'url' => 'https://t4.ftcdn.net/jpg/00/81/21/47/240_F_81214721_xOiCCpEmKNPoLSPXo3UwLP7i8HPiLM78.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/00/81/21/47/500_F_81214721_xOiCCpEmKNPoLSPXo3UwLP7i8HPiLM78.jpg',
                            'width' => 4928,
                            'height' => 3264
                        ),
                    4 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 213910590,
                            'title' => 'White painted monstera tropical leaf with dripping paint',
                            'url' => 'https://t4.ftcdn.net/jpg/02/13/91/05/240_F_213910590_bQT5UjUHOLWMmCiDm9AAYR6js4QCerR6.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/02/13/91/05/500_F_213910590_bQT5UjUHOLWMmCiDm9AAYR6js4QCerR6.jpg',
                            'width' => 3086,
                            'height' => 4628
                        ),
                    5 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 180705946,
                            'title' => 'daughter hugging soldier',
                            'url' => 'https://t4.ftcdn.net/jpg/01/80/70/59/240_F_180705946_8bbxWnPUDUuLI2uHqEPqJTD7BHx2BB19.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/80/70/59/500_F_180705946_8bbxWnPUDUuLI2uHqEPqJTD7BHx2BB19.jpg',
                            'width' => 7360,
                            'height' => 4912,
                            'overlay' => 'Special'
                        ),
                    6 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 126067012,
                            'title' => 'Christmas Tree Farm',
                            'url' => 'https://t3.ftcdn.net/jpg/01/26/06/70/240_F_126067012_VDHo0DXpzyUgJLXP5BHzmUvQCgM52q3O.jpg',
                            'preview_url' => 'https://as1.ftcdn.net/jpg/01/26/06/70/500_F_126067012_VDHo0DXpzyUgJLXP5BHzmUvQCgM52q3O.jpg',
                            'width' => 5373,
                            'height' => 3582
                        ),
                    7 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 246811446,
                            'title' => '【日本の温泉】草津温泉の湯畑',
                            'url' => 'https://t3.ftcdn.net/jpg/02/46/81/14/240_F_246811446_iwTs5N49KbtB6jmLcdSeC12X1b1NGh9p.jpg',
                            'preview_url' => 'https://as1.ftcdn.net/jpg/02/46/81/14/500_F_246811446_iwTs5N49KbtB6jmLcdSeC12X1b1NGh9p.jpg',
                            'width' => 6240,
                            'height' => 4160
                        ),
                    8 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 996327,
                            'title' => 'ice cold ice',
                            'url' => 'https://t4.ftcdn.net/jpg/00/00/99/63/240_F_996327_MnAK8eJblv567H457lHakPCVAScQak.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/00/00/99/63/500_F_996327_MnAK8eJblv567H457lHakPCVAScQak.jpg',
                            'width' => 3888,
                            'height' => 2592
                        ),
                    9 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 201953179,
                            'title' => 'Portrait of black woman with dreadlocks hair',
                            'url' => 'https://t4.ftcdn.net/jpg/02/01/95/31/240_F_201953179_LxKXBQSL9wd6L4mKdjKrLl8wx1WlhLtt.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/02/01/95/31/500_F_201953179_LxKXBQSL9wd6L4mKdjKrLl8wx1WlhLtt.jpg',
                            'width' => 6000,
                            'height' => 4006
                        ),
                    10 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 99095345,
                            'title' => '鶴',
                            'url' => 'https://t4.ftcdn.net/jpg/00/99/09/53/240_F_99095345_D355ceiLff0ZwJnWMaDTUwB1zs2Fej1i.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/00/99/09/53/500_F_99095345_D355ceiLff0ZwJnWMaDTUwB1zs2Fej1i.jpg',
                            'width' => 3996,
                            'height' => 2664
                        ),
                    11 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 265222752,
                            'title' => 'two athletic man floats on a red boat in river',
                            'url' => 'https://t4.ftcdn.net/jpg/02/65/22/27/240_F_265222752_FlbEzWyiqSjY8RiPfitazMOUuNBpLQ1R.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/02/65/22/27/500_F_265222752_FlbEzWyiqSjY8RiPfitazMOUuNBpLQ1R.jpg',
                            'width' => 5464,
                            'height' => 3640,
                            'overlay' => 'Special'
                        ),
                    12 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 139205108,
                            'title' => 'White bear portrait close up isolated on black background',
                            'url' => 'https://t4.ftcdn.net/jpg/01/39/20/51/240_F_139205108_XAQcMphYUksGE73JVMcEgRHNwA3BBCVZ.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/39/20/51/500_F_139205108_XAQcMphYUksGE73JVMcEgRHNwA3BBCVZ.jpg',
                            'width' => 4256,
                            'height' => 2832
                        ),
                    13 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 193601733,
                            'title' => 'hair flowing in the wind, a woman and a cold day',
                            'url' => 'https://t4.ftcdn.net/jpg/01/93/60/17/240_F_193601733_Kyppgfs8JMn1JTiYgtaMoGTTWrxW0LZB.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/93/60/17/500_F_193601733_Kyppgfs8JMn1JTiYgtaMoGTTWrxW0LZB.jpg',
                            'width' => 5676,
                            'height' => 3376
                        ),
                    14 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 230607584,
                            'title' => 'Man hand holding wooden honey dipper, honey spoon on top of glass of tea/ medicine and dripping honey in hot tea. Knitted socks, small jar of honey, garlic on wooden table against black background.',
                            'url' => 'https://t4.ftcdn.net/jpg/02/30/60/75/240_F_230607584_ctoB3XyGIIspoO1vzhp3c3F5hAcjGqhj.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/02/30/60/75/500_F_230607584_ctoB3XyGIIspoO1vzhp3c3F5hAcjGqhj.jpg',
                            'width' => 5275,
                            'height' => 3432
                        ),
                    15 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 168034758,
                            'title' => 'African businessman standing at start up',
                            'url' => 'https://t4.ftcdn.net/jpg/01/68/03/47/240_F_168034758_99ignarFY2WWhoeMfrVtrkngRvOHd6yc.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/68/03/47/500_F_168034758_99ignarFY2WWhoeMfrVtrkngRvOHd6yc.jpg',
                            'width' => 5792,
                            'height' => 8688
                        ),
                    16 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 208330713,
                            'title' => 'Hygge concept with cat, book and coffee in the bed',
                            'url' => 'https://t4.ftcdn.net/jpg/02/08/33/07/240_F_208330713_OnmjJmlMHjtCrHVv1cLzzXg8tNXcStQg.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/02/08/33/07/500_F_208330713_OnmjJmlMHjtCrHVv1cLzzXg8tNXcStQg.jpg',
                            'width' => 5473,
                            'height' => 3649,
                            'overlay' => 'Special'
                        ),
                    17 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 74443764,
                            'title' => 'beautiful little girl with gifts on a windowsill',
                            'url' => 'https://t4.ftcdn.net/jpg/00/74/44/37/240_F_74443764_8Ghf6q8zQIwgbbOctbkVT0t3nl40xg3v.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/00/74/44/37/500_F_74443764_8Ghf6q8zQIwgbbOctbkVT0t3nl40xg3v.jpg',
                            'width' => 3840,
                            'height' => 4351
                        ),
                    18 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 232892201,
                            'title' => 'Happy grandmother hugging her grandson',
                            'url' => 'https://t3.ftcdn.net/jpg/02/32/89/22/240_F_232892201_uSHFgfpRNUv1w2mnYNJzWpIKYgwyu2yf.jpg',
                            'preview_url' => 'https://as1.ftcdn.net/jpg/02/32/89/22/500_F_232892201_uSHFgfpRNUv1w2mnYNJzWpIKYgwyu2yf.jpg',
                            'width' => 7000,
                            'height' => 4853
                        ),
                    19 =>
                        array(
                            'id_field_name' => 'id',
                            'id' => 194424195,
                            'title' => 'Snowscape texture',
                            'url' => 'https://t4.ftcdn.net/jpg/01/94/42/41/240_F_194424195_ymCmACmKlV0wj6iYLey9XStod3VUpCye.jpg',
                            'preview_url' => 'https://as2.ftcdn.net/jpg/01/94/42/41/500_F_194424195_ymCmACmKlV0wj6iYLey9XStod3VUpCye.jpg',
                            'width' => 4608,
                            'height' => 3282
                        ),
                ),
            'totalRecords' => 347,
        );
    }
}
