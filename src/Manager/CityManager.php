<?php

namespace App\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\City;
use Helper\RegexHelper;

/**
 * Class CityManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class CityManager
{
    protected $em;

    protected $connection;

    protected $logger;

    protected $regexHelper;

    protected $cities = array();

    public function get($city)
    {
        return $this->getEm()->getRepository('App:City')->findOneById($city);
    }

    public function getGoogleId($city)
    {
        $city = $this->getEm()->getRepository('App:City')->findOneById($city);

        return $city->getGoogleId();
    }

    public function getCityByGoogleId($googleId)
    {
        return $this->getEm()->getRepository('App:City')->findOneByGoogleId($googleId);
    }

    public function getMeetings($city)
    {
        $meetings = $this->getEm()->getRepository('App:MeetingPoint')->findByCity($city);

        return $meetings;
    }

    protected function createCity(array $cityV1)
    {
        $city = new City();
        foreach ($cityV1 as $key => $value) {
            $setter = $this->getRegexHelper()->setCamelCase('set' . ucfirst($key));
            if (method_exists($city, $setter)) {
                if (in_array($setter, array('setCreatedAt', 'setUpdatedAt'))) {
                    $city->$setter(new \DateTime($value));
                }
            }
        }

        $address = str_replace(' ', '+', $cityV1['address']);

        // $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=false";
        $url = "https://maps.googleapis.com/maps/api/geocode/json?place_id=" . $cityV1['id'] . "&key=AIzaSyA6W60RJIPG4s9jVW_jeGiewnSPcJKlj5Y";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        $return = json_decode($result, true);

        curl_close($ch);
        // $city
        //     ->setNorth($cityV1['upright_lat'])
        //     ->setSouth($cityV1['downleft_lat'])
        //     ->setEast($cityV1['upright_long'])
        //     ->setWest($cityV1['downleft_long'])
        //     ->setLng($cityV1['latitude'])
        //     ->setLat($cityV1['longitude'])
        //     ->setTitle($cityV1['address'])
        //     ->setGoogleId($cityV1['id']);
        if (isset($return['results'][0])) {
            $this->logger->info('curl maps', array('result' => $return['results'][0]));
            $latitude = $return['results'][0]['geometry']['location']['lat'];
            $longitude = $return['results'][0]['geometry']['location']['lng'];
            $city
                ->setLng($longitude)
                ->setLat($latitude)
                ->setTitle($return['results'][0]['address_components'][0]['long_name'])
                ->setGoogleId($return['results'][0]['place_id']);

            if (isset($return['results'][0]['geometry']['bounds'])) {
                $north = $return['results'][0]['geometry']['bounds']['northeast']['lat'];
                $south = $return['results'][0]['geometry']['bounds']['southwest']['lat'];
                $east = $return['results'][0]['geometry']['bounds']['northeast']['lng'];
                $west = $return['results'][0]['geometry']['bounds']['southwest']['lng'];
                $city
                    ->setNorth($north)
                    ->setSouth($south)
                    ->setEast($east)
                    ->setWest($west);
            }
        }

        return $city;
    }

    public function registerCitiesOld()
    {
        $citiesInDB = $this->getEm()->getRepository('App:City')->findAll();
        if (!empty($citiesInDB)) {
            $this->logger->warning(
                'Data already here'
            );
            foreach ($citiesInDB as $cityInDB) {
                $this->addCity($cityInDB->getGoogleId(), $cityInDB->getId());
            }
            return $this->getCities();
        }


        $cities = new \ArrayIterator(
            $this->getConnection()->fetchAll(
                'SELECT *
                FROM city c'
            )
        );
        $count = 0;
        $total = $cities->count();
        $this->logger->info(
            'import.table.city.array.cities',
            array(
                'total' => $total
            )
        );
        while ($cities->valid()) {
            $this->getEm()->getConnection()->beginTransaction();
            try {
                $cityV1 = $cities->current();
                $city = $this->createCity($cityV1);
                $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
                $this->logger->info(
                    'cities v1 / v2',
                    array(
                        'city_old' => $cityV1,
                        'city' => $serializer->toArray($city)
                    )
                );
                $this->getEm()->persist($city);
                $this->getEm()->flush();
                $this->getEm()->getConnection()->commit();
                $this->addCity($city->getGoogleId(), $city->getId());
                $count++;
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.city.insert.query.error',
                    array(
                        'city' => $cities->current(),
                        'message' => $e->getMessage(),
                        'message' => $e->getTraceAsString(),
                    )
                );
                throw $e;
            }
            $cities->next();
        }
    }

    public function registerCities()
    {
        $cities = array(
            array('00020562-4f0b-11e6-84b7-c8cbb8b4347f', 'Betton', 'ChIJ3Uy0aVDcDkgRMLjkNs2lDAQ', -1.643459, 48.182829, 48.2100609, 48.149217, -1.598325, -1.6970089, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('0014b819-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Ismier', 'ChIJ3VDekZNYikcRgGu-5CqrCAQ', 5.828113, 45.248534, 45.284064, 45.2165709, 5.8557011, 5.799076, '2015-11-22 18:14:21', '2015-11-22 18:14:21'),
            array('0027434c-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Nicolas de Véroce', 'ChIJ3xfatPxZiUcRqI9uFeMS-KQ', 6.722842, 45.855023, null, null, null, null, '2015-11-26 22:08:59', '2015-11-26 22:08:59'),
            array('003a0ae6-4f0b-11e6-84b7-c8cbb8b4347f', 'Méaudre', 'ChIJ4-JpOXmTikcR0HW-5CqrCAQ', 5.526489, 45.126924, 45.1640019, 45.0985919, 5.578581, 5.4907651, '2015-11-22 19:16:56', '2015-11-22 19:16:56'),
            array('004da743-4f0b-11e6-84b7-c8cbb8b4347f', 'Poissy', 'ChIJ42Xt3UeJ5kcRoDmMaMOCCwQ', 2.046982, 48.929584, 48.9569219, 48.8911179, 2.0707531, 1.9793091, '2016-02-28 22:11:08', '2016-02-28 22:11:08'),
            array('00601ab9-4f0b-11e6-84b7-c8cbb8b4347f', 'Orléans', 'ChIJ44bzndTk5EcRKYDDeeR9uZ4', 1.909251, 47.902964, 47.9335359, 47.813296, 1.948677, 1.8757401, '2015-11-22 18:16:32', '2015-11-22 18:16:32'),
            array('00733ceb-4f0b-11e6-84b7-c8cbb8b4347f', 'Grans', 'ChIJ473VbOwBthIR0AKX_aUZCAQ', 5.063777, 43.607073, 43.6429449, 43.5914969, 5.097386, 4.988797, '2015-11-22 18:30:48', '2015-11-22 18:30:48'),
            array('00865a67-4f0b-11e6-84b7-c8cbb8b4347f', 'Alès', 'ChIJ49i3h0JCtBIRMEhrFiGIBwQ', 4.083352, 44.127204, 44.1562589, 44.094086, 4.1273149, 4.053569, '2015-11-22 20:43:14', '2015-11-22 20:43:14'),
            array('00990389-4f0b-11e6-84b7-c8cbb8b4347f', 'Auxerre', 'ChIJ4a81tzBP7kcRkSeiunpT5i4', 3.573781, 47.798202, 47.8524079, 47.7353409, 3.6375551, 3.5260921, '2015-11-22 18:15:07', '2015-11-22 18:15:07'),
            array('00ac0329-4f0b-11e6-84b7-c8cbb8b4347f', 'Mont-Saint-Aignan', 'ChIJ4bZFCZbd4EcRkHi2T0gUDAQ', 1.079798, 49.459778, 49.4837579, 49.4508399, 1.10242, 1.058693, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('00bf0a21-4f0b-11e6-84b7-c8cbb8b4347f', 'Champigny', 'ChIJ4eADbsUU70cRMGkNszTOCQQ', 3.127702, 48.3197199, 48.338249, 48.2708109, 3.173991, 3.0955849, '2015-11-22 18:05:44', '2015-11-22 18:05:44'),
            array('00d27d23-4f0b-11e6-84b7-c8cbb8b4347f', 'Périgueux', 'ChIJ4Q5KlAFx_0cRhfeOA57Ps-A', 0.7211149, 45.184029, 45.213861, 45.173886, 0.747362, 0.6735409, '2016-03-19 15:24:13', '2016-03-19 15:24:13'),
            array('00e502e6-4f0b-11e6-84b7-c8cbb8b4347f', 'Belfort', 'ChIJ4UhO_Hg7kkcR0FANszTOCQQ', 6.863849, 47.639674, 47.670928, 47.620334, 6.894894, 6.787417, '2015-11-22 18:55:16', '2015-11-22 18:55:16'),
            array('00f99273-4f0b-11e6-84b7-c8cbb8b4347f', '78000', 'ChIJ4VqV9L595kcRMHLY4caCCxw', 2.1219587, 48.8051741, 48.8285917, 48.7791767, 2.1684093, 2.0702618, '2016-02-01 10:55:26', '2016-02-01 10:55:26'),
            array('010c4a3b-4f0b-11e6-84b7-c8cbb8b4347f', 'Décines-Charpieu', 'ChIJ4zba6crA9EcRsBi75CqrCAQ', 4.957024, 45.7674499, 45.807593, 45.744253, 4.985367, 4.932504, '2015-11-22 19:53:10', '2015-11-22 19:53:10'),
            array('011f92d6-4f0b-11e6-84b7-c8cbb8b4347f', 'Châtel', 'ChIJ5-lQX1ChjkcREKO65CqrCAQ', 6.841152, 46.267395, 46.292536, 46.2033489, 6.8649001, 6.7725551, '2015-11-22 17:40:06', '2015-11-22 17:40:06'),
            array('01321af1-4f0b-11e6-84b7-c8cbb8b4347f', 'Moret-sur-Loing', 'ChIJ56TpFs1f70cRFUTi3l5Lkg8', 2.816429, 48.373347, 48.3832441, 48.343431, 2.829728, 2.7913569, '2015-11-22 18:05:44', '2015-11-22 18:05:44'),
            array('0145f4bb-4f0b-11e6-84b7-c8cbb8b4347f', 'Aussois', 'ChIJ57ml2ICQiUcRcLi65CqrCAQ', 6.741517, 45.227947, 45.297921, 45.1984659, 6.7728991, 6.68035, '2015-12-24 15:42:58', '2015-12-24 15:42:58'),
            array('0159f2e0-4f0b-11e6-84b7-c8cbb8b4347f', 'Suresnes', 'ChIJ584OtMVk5kcR4DyLaMOCCwQ', 2.219033, 48.869798, 48.88276, 48.859284, 2.2364639, 2.199768, '2015-11-22 18:08:55', '2015-11-22 18:08:55'),
            array('016f1c4a-4f0b-11e6-84b7-c8cbb8b4347f', 'Lausanne', 'ChIJ5aeJzT4pjEcRXu7iysk_F-s', 6.6322734, 46.5196535, 46.6023299, 46.5042983, 6.7208401, 6.58415, '2016-01-22 10:02:40', '2016-01-22 10:02:40'),
            array('0181c9ec-4f0b-11e6-84b7-c8cbb8b4347f', 'La Ciotat', 'ChIJ5bjorGOvyRIR0AOX_aUZCAQ', 5.605155, 43.173653, 43.218904, 43.1573841, 5.679111, 5.555955, '2016-01-11 15:01:56', '2016-01-11 15:01:56'),
            array('0199fc8c-4f0b-11e6-84b7-c8cbb8b4347f', 'Puget-sur-Argens', 'ChIJ5d1_XA2jzhIRkMqP_aUZCAQ', 6.68408, 43.455329, 43.5134389, 43.4290109, 6.715503, 6.6567439, '2015-11-22 19:47:14', '2015-11-22 19:47:14'),
            array('01ad41cc-4f0b-11e6-84b7-c8cbb8b4347f', 'Serbonnes', 'ChIJ5QVblNwV70cREFcNszTOCQQ', 3.203207, 48.320312, 48.352491, 48.308243, 3.232134, 3.1794011, '2015-11-22 18:05:44', '2015-11-22 18:05:44'),
            array('01c0880d-4f0b-11e6-84b7-c8cbb8b4347f', 'Chamonix', 'ChIJ5y7-LQZMiUcRgKO65CqrCAQ', 6.869433, 45.923697, 46.031, 45.8331265, 7.044201, 6.81372, '2015-11-22 15:31:54', '2015-11-22 15:31:54'),
            array('01d46bfe-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Martin-de-Belleville', 'ChIJ5ZdICA8qikcR8Kq65CqrCAQ', 6.504104, 45.3804019, 45.4486129, 45.2519109, 6.6245031, 6.4151761, '2015-11-22 19:04:55', '2015-11-22 19:04:55'),
            array('01e6b86d-4f0b-11e6-84b7-c8cbb8b4347f', 'Geneva', 'ChIJ6-LQkwZljEcRObwLezWVtqA', 6.1431577, 46.2043907, 46.232399, 46.1776599, 6.177857, 6.1103201, '2016-01-19 16:35:42', '2016-01-19 16:35:42'),
            array('02159f0e-4f0b-11e6-84b7-c8cbb8b4347f', 'Laruns', 'ChIJ63Y-OOi6Vw0R-jIH5UcQTBU', -0.426274, 42.987644, 43.018672, 42.795479, -0.307032, -0.510254, '2015-11-22 18:19:26', '2015-11-22 18:19:26'),
            array('022a398c-4f0b-11e6-84b7-c8cbb8b4347f', 'Seyssinet-Pariset', 'ChIJ6R9AoCbzikcRYGa-5CqrCAQ', 5.6703133, 45.1729516, 45.1907319, 45.153409, 5.7015459, 5.6387341, '2015-11-22 17:37:12', '2015-11-22 17:37:12'),
            array('0240752f-4f0b-11e6-84b7-c8cbb8b4347f', 'Lattes', 'ChIJ6TQFgwOwthIRAMdqFiGIBwQ', 3.896473, 43.567296, 43.5956949, 43.5382519, 3.950391, 3.8476349, '2015-11-22 17:59:48', '2015-11-22 17:59:48'),
            array('027007a2-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Marais', 'ChIJ6UrOzQNu5kcRRp5vRIClzzg', 2.3588038, 48.8587029, 48.867456, 48.8511795, 2.3691334, 2.3506656, '2016-03-17 20:35:34', '2016-03-17 20:35:34'),
            array('02856674-4f0b-11e6-84b7-c8cbb8b4347f', '64000', 'ChIJ6XpctIVIVg0RoKSvkRplBhw', -0.3465971, 43.3192848, 43.3578704, 43.285194, -0.2942968, -0.392249, '2016-01-18 19:16:00', '2016-01-18 19:16:00'),
            array('02991482-4f0b-11e6-84b7-c8cbb8b4347f', 'Pau', 'ChIJ6XpctIVIVg0RsJQTSBdlBgQ', -0.370797, 43.2951, 43.3580329, 43.285778, -0.294777, -0.3925371, '2015-11-22 18:19:26', '2015-11-22 18:19:26'),
            array('02ad7c1c-4f0b-11e6-84b7-c8cbb8b4347f', 'Vaulnaveys-le-Haut', 'ChIJ70MVXExgikcRW4G5o5l9s-g', 5.811073, 45.119296, 45.140177, 45.091553, 5.8773551, 5.7941511, '2015-11-22 16:31:44', '2015-11-22 16:31:44'),
            array('02c76e79-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Gervais-les-Bains', 'ChIJ75odSANXiUcRd6tzAmzNWaY', 6.712187, 45.892013, 45.91024, 45.8005449, 6.853841, 6.657202, '2015-11-22 19:52:07', '2015-11-22 19:52:07'),
            array('02db08a4-4f0b-11e6-84b7-c8cbb8b4347f', 'Argelès-sur-Mer', 'ChIJ7caKZ9V-sBIRMBgi4G7BtAs', 3.022911, 42.546214, 42.593697, 42.4665849, 3.075856, 2.970513, '2015-11-22 20:49:22', '2015-11-22 20:49:22'),
            array('02ef19a0-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Christophe-en-Oisans', 'ChIJ7Ql_KxIPikcR0Gy-5CqrCAQ', 6.2192189, 44.9429427, 45.012531, 44.848374, 6.359308, 6.106189, '2015-11-22 19:16:56', '2015-11-22 19:16:56'),
            array('0303e199-4f0b-11e6-84b7-c8cbb8b4347f', 'Lorient', 'ChIJ7QlmqlZeEEgR6uTNDHdnM6k', -3.3702449, 47.7482524, 47.774454, 47.724196, -3.3501088, -3.41052, '2016-01-22 14:44:30', '2016-01-22 14:44:30'),
            array('031822ca-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Gets', 'ChIJ7RzWhigDjEcRM1Me3BXSzDs', 6.669988, 46.158622, 46.191169, 46.1195099, 6.6993341, 6.614858, '2015-11-22 16:18:28', '2015-11-22 16:18:28'),
            array('032c4c8c-4f0b-11e6-84b7-c8cbb8b4347f', 'Hyères', 'ChIJ7xx-cKIjyRIRa-2UHyGuwWk', 6.128639, 43.120541, 43.2070129, 42.981998, 6.5327139, 6.0678239, '2016-02-10 17:57:27', '2016-02-10 17:57:27'),
            array('03403c0e-4f0b-11e6-84b7-c8cbb8b4347f', 'Reignier-Esery', 'ChIJ80my7t5zjEcR4Jm65CqrCAQ', 6.2666019, 46.138178, 46.1637209, 46.109618, 6.312404, 6.216887, '2015-11-22 17:36:09', '2015-11-22 17:36:09'),
            array('03549082-4f0b-11e6-84b7-c8cbb8b4347f', 'Passy', 'ChIJ85zJZHFUiUcRkJq65CqrCAQ', 6.684919, 45.923392, 46.018209, 45.897603, 6.8554391, 6.6459561, '2015-11-26 22:08:59', '2015-11-26 22:08:59'),
            array('036b1692-4f0b-11e6-84b7-c8cbb8b4347f', 'Eyguières', 'ChIJ89CkAEr-tRIRYAOX_aUZCAQ', 5.02967, 43.695299, 43.753018, 43.6479789, 5.0729209, 4.9534379, '2015-11-22 18:30:48', '2015-11-22 18:30:48'),
            array('03815477-4f0b-11e6-84b7-c8cbb8b4347f', 'Gérardmer', 'ChIJ8ep-M1DGk0cR0IE3mrlfCgQ', 6.877292, 48.070081, 48.107444, 48.024099, 6.93016, 6.7760739, '2015-11-22 20:39:11', '2015-11-22 20:39:11'),
            array('0393135f-4f0b-11e6-84b7-c8cbb8b4347f', 'Sweden', 'ChIJ8fA1bTmyXEYRYm-tjaLruCI', 18.643501, 60.128161, 69.0599709, 55.3367024, 24.1668092, 10.9631866, '2015-12-02 15:31:44', '2015-12-02 15:31:44'),
            array('03a5d4f4-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Menuires', 'ChIJ8SEnPkCHiUcRyYp2ciQ_xHs', 6.536145, 45.326745, null, null, null, null, '2016-01-26 20:26:51', '2016-01-26 20:26:51'),
            array('03b9dcd5-4f0b-11e6-84b7-c8cbb8b4347f', 'Grimaud', 'ChIJ8Uf10LbJzhIROUPytUxds9Q', 6.521865, 43.274308, 43.3104639, 43.253996, 6.6205863, 6.3987149, '2016-01-19 14:01:24', '2016-01-19 14:01:24'),
            array('03cf2de6-4f0b-11e6-84b7-c8cbb8b4347f', 'Colombier-Saugnieu', 'ChIJ8yM4jEjJ9EcRd53pJ61Q7GQ', 5.113632, 45.708962, 45.739725, 45.695005, 5.160109, 5.056694, '2015-11-22 20:33:59', '2015-11-22 20:33:59'),
            array('03e23084-4f0b-11e6-84b7-c8cbb8b4347f', 'Villejuif', 'ChIJ8zhyol9x5kcRgDeLaMOCCwQ', 2.359279, 48.792716, 48.808054, 48.778683, 2.376832, 2.343095, '2015-11-22 20:54:31', '2015-11-22 20:54:31'),
            array('03f56d30-4f0b-11e6-84b7-c8cbb8b4347f', 'Aurillac', 'ChIJ914hCZdVrRIRXqU-OUgfSMg', 2.444997, 44.930953, 44.966036, 44.890088, 2.492908, 2.3904769, '2015-11-22 19:45:53', '2015-11-22 19:45:53'),
            array('0408eea1-4f0b-11e6-84b7-c8cbb8b4347f', 'Gouvieux', 'ChIJ94RmtplI5kcRouWq0UCPTPc', 2.409336, 49.192878, 49.2136499, 49.166222, 2.4630631, 2.369142, '2015-11-22 18:09:40', '2015-11-22 18:09:40'),
            array('041bb186-4f0b-11e6-84b7-c8cbb8b4347f', 'Montigny-lès-Metz', 'ChIJ96a3B4vblEcRCOnBt28jBbA', 6.147807, 49.099068, 49.114242, 49.0790109, 6.1820471, 6.127466, '2015-11-22 19:12:11', '2015-11-22 19:12:11'),
            array('042e4f7a-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Allues', 'ChIJ96kFkN1_iUcRPAEcWeYZul8', 6.556993, 45.432268, 45.45337, 45.27467, 6.664549, 6.520614, '2015-11-22 18:18:45', '2015-11-22 18:18:45'),
            array('04414f06-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Crosets', 'ChIJ97mObLiljkcR2syrBbMBPXk', 6.8367313, 46.1851259, null, null, null, null, '2016-02-04 15:20:58', '2016-02-04 15:20:58'),
            array('04549876-4f0b-11e6-84b7-c8cbb8b4347f', 'Vaujany', 'ChIJ9d7g-OFAikcREGS-5CqrCAQ', 6.073805, 45.15738, 45.2442759, 45.132604, 6.1619241, 6.037045, '2015-11-22 18:11:21', '2015-11-22 18:11:21'),
            array('046dc4b5-4f0b-11e6-84b7-c8cbb8b4347f', 'Beuil', 'ChIJ9e5TA0dRzBIRb7D1cYzopPo', 6.990253, 44.0942239, 44.1966899, 44.0384629, 7.028372, 6.910142, '2015-11-22 19:05:32', '2015-11-22 19:05:32'),
            array('0488e307-4f0b-11e6-84b7-c8cbb8b4347f', 'Meribel Airport', 'ChIJ9UA_PymAiUcRwK86jYqn53E', 6.5784707, 45.4066681, null, null, null, null, '2016-02-18 06:54:41', '2016-02-18 06:54:41'),
            array('049e99b2-4f0b-11e6-84b7-c8cbb8b4347f', '15th arrondissement of Paris', 'ChIJ9WBuGRRw5kcRMBuUaMOCCwU', 2.2927665, 48.8421616, 48.8581791, 48.8251341, 2.324624, 2.262783, '2016-03-01 15:58:26', '2016-03-01 15:58:26'),
            array('04b37785-4f0b-11e6-84b7-c8cbb8b4347f', 'Vizille', 'ChIJA2lgbwRiikcR4GG-5CqrCAQ', 5.772412, 45.077318, 45.097412, 45.0482199, 5.807798, 5.7545381, '2016-02-23 12:33:36', '2016-02-23 12:33:36'),
            array('04c71b2c-4f0b-11e6-84b7-c8cbb8b4347f', 'Mulhouse', 'ChIJa5SIHWybkUcRYDM5mrlfCgQ', 7.335888, 47.750839, 47.7834759, 47.7218959, 7.3687281, 7.2825211, '2015-11-22 19:07:39', '2015-11-22 19:07:39'),
            array('04db201b-4f0b-11e6-84b7-c8cbb8b4347f', 'Oise', 'ChIJa7kkjRq050cRQClhgT7xCgM', 2.4146396, 49.4214568, 49.763922, 49.060525, 3.166125, 1.688866, '2016-02-02 15:54:39', '2016-02-02 15:54:39'),
            array('04edfe43-4f0b-11e6-84b7-c8cbb8b4347f', 'Sète', 'ChIJa8cuUJY1sRIRAmYnNvfhb5o', 3.7008219, 43.4078758, 43.430248, 43.3260584, 3.7314646, 3.5529929, '2015-11-23 21:20:36', '2015-11-23 21:20:36'),
            array('05017481-4f0b-11e6-84b7-c8cbb8b4347f', 'Seynod', 'ChIJA9EIOrOai0cRXL5-7WoDX-Q', 6.1020358, 45.8890357, 45.895445, 45.8281139, 6.1186581, 6.048413, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('0513834f-4f0b-11e6-84b7-c8cbb8b4347f', 'Italy', 'ChIJA9KNRIL-1BIRb15jJFz1LOI', 12.56738, 41.87194, 47.0919999, 35.4930062, 18.5205014, 6.6267201, '2016-05-19 16:54:28', '2016-05-19 16:54:28'),
            array('0528c6c4-4f0b-11e6-84b7-c8cbb8b4347f', 'La Plagne', 'ChIJabq99rt7iUcRAG446ZK0_kA', 6.6767908, 45.506956, null, null, null, null, '2015-11-22 16:17:01', '2015-11-22 16:17:01'),
            array('053b49b3-4f0b-11e6-84b7-c8cbb8b4347f', 'Meudon', 'ChIJAe9HWkt65kcRgD2LaMOCCwQ', 2.23847, 48.812995, 48.823993, 48.7815799, 2.254991, 2.2025591, '2015-11-22 20:54:31', '2015-11-22 20:54:31'),
            array('054e9758-4f0b-11e6-84b7-c8cbb8b4347f', 'Morgins', 'ChIJAQpIEIajjkcRY1NIHMKJv_s', 6.8532, 46.23624, null, null, null, null, '2016-02-04 15:20:58', '2016-02-04 15:20:58'),
            array('05617878-4f0b-11e6-84b7-c8cbb8b4347f', 'Remiremont', 'ChIJaUtxAC-uk0cRgHY3mrlfCgQ', 6.591716, 48.015589, 48.027626, 47.9798939, 6.619127, 6.542861, '2015-11-22 20:47:50', '2015-11-22 20:47:50'),
            array('05748361-4f0b-11e6-84b7-c8cbb8b4347f', 'Val-de-Marne', 'ChIJaxiZwqcM5kcRYCuLaMOCCwM', 2.4740337, 48.7931426, 48.861484, 48.6876429, 2.615642, 2.308676, '2016-01-18 12:39:54', '2016-01-18 12:39:54'),
            array('05871ad1-4f0b-11e6-84b7-c8cbb8b4347f', 'Annemasse', 'ChIJayauCipujEcR7W20_mIDW5w', 6.234158, 46.193253, 46.2019419, 46.176127, 6.2786071, 6.216615, '2015-11-22 17:36:09', '2015-11-22 17:36:09'),
            array('059b16c5-4f0b-11e6-84b7-c8cbb8b4347f', '5th arrondissement', 'ChIJb_T3b-9x5kcRkBqUaMOCCwU', 2.3518339, 48.8434912, 48.854086, 48.836802, 2.3660681, 2.336664, '2015-11-22 18:13:36', '2015-11-22 18:13:36'),
            array('05afcd02-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Grand-Quevilly', 'ChIJb-JD4ZHg4EcRoIC2T0gUDAQ', 1.041384, 49.411811, 49.4318763, 49.391173, 1.071067, 1.0133829, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('05c39121-4f0b-11e6-84b7-c8cbb8b4347f', 'Darnétal', 'ChIJb02cWPnb4EcRYIe2T0gUDAQ', 1.153222, 49.446786, 49.46025, 49.4333889, 1.1768611, 1.12992, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('05d79b67-4f0b-11e6-84b7-c8cbb8b4347f', 'Courson-Monteloup', 'ChIJb1Yu-JXT5UcRkEeLaMOCCwQ', 2.14371, 48.598137, 48.610639, 48.5873119, 2.158992, 2.118794, '2015-11-22 20:30:01', '2015-11-22 20:30:01'),
            array('05ebbe39-4f0b-11e6-84b7-c8cbb8b4347f', 'Albertville', 'ChIJb3z1EwnDi0cRV4EOYVAVjRI', 6.392726, 45.675535, 45.6882919, 45.6411259, 6.4602471, 6.359654, '2015-11-22 17:46:29', '2015-11-22 17:46:29'),
            array('05ff430f-4f0b-11e6-84b7-c8cbb8b4347f', 'Clermont-Ferrand', 'ChIJB4Uuf90b90cRIm-gOGAwlDk', 3.087025, 45.777222, 45.818374, 45.7557549, 3.1721561, 3.053296, '2015-11-22 19:45:53', '2015-11-22 19:45:53'),
            array('06122e27-4f0b-11e6-84b7-c8cbb8b4347f', 'Avignon', 'ChIJB528OYfrtRIRNnsd-m6bQuY', 4.805528, 43.949317, 43.9966409, 43.886473, 4.927226, 4.739279, '2015-11-22 17:39:09', '2015-11-22 17:39:09'),
            array('06266742-4f0b-11e6-84b7-c8cbb8b4347f', 'Lodève', 'ChIJB5mNJWJisRIRMMZqFiGIBwQ', 3.313975, 43.73366, 43.76403, 43.694231, 3.3423729, 3.2521938, '2016-04-25 14:59:35', '2016-04-25 14:59:35'),
            array('06393fcd-4f0b-11e6-84b7-c8cbb8b4347f', 'Claix', 'ChIJB6CfH1SMikcRMHy-5CqrCAQ', 5.672925, 45.1199439, 45.147492, 45.0890499, 5.700797, 5.6180501, '2016-05-01 16:24:28', '2016-05-01 16:24:28'),
            array('064cb309-4f0b-11e6-84b7-c8cbb8b4347f', 'Grenoble', 'ChIJb76J1ov0ikcRmFOZbs0QjGE', 5.724524, 45.188529, 45.214326, 45.154005, 5.7530811, 5.6780039, '2015-11-22 17:35:56', '2015-11-22 17:35:56'),
            array('0666fa77-4f0b-11e6-84b7-c8cbb8b4347f', 'Lamasquère', 'ChIJb7Y7Gni1rhIRACJBL5z2BgQ', 1.249912, 43.485804, 43.497063, 43.4744649, 1.2749679, 1.224998, '2015-11-22 19:02:54', '2015-11-22 19:02:54'),
            array('067a2f92-4f0b-11e6-84b7-c8cbb8b4347f', 'Pringy', 'ChIJb81CnMGFi0cREJq65CqrCAQ', 6.121892, 45.946463, 45.976711, 45.932411, 6.139617, 6.07486, '2015-11-22 19:03:43', '2015-11-22 19:03:43'),
            array('068eb3e4-4f0b-11e6-84b7-c8cbb8b4347f', 'Fleury-Mérogis', 'ChIJbcviZJXe5UcRkEaLaMOCCwQ', 2.36675, 48.630742, 48.647961, 48.6174129, 2.3930461, 2.3493981, '2015-11-22 19:21:20', '2015-11-22 19:21:20'),
            array('06a2b9f1-4f0b-11e6-84b7-c8cbb8b4347f', 'Tignieu-Jameyzieu', 'ChIJbSmAoRPL9EcRQGW-5CqrCAQ', 5.185379, 45.733945, 45.7631719, 45.701017, 5.210861, 5.155051, '2015-11-22 20:33:59', '2015-11-22 20:33:59'),
            array('06b5d327-4f0b-11e6-84b7-c8cbb8b4347f', 'Vénissieux', 'ChIJbTBY7FzC9EcRCc4eTX4iX-E', 4.8844649, 45.699594, 45.7307299, 45.67162, 4.911351, 4.8497121, '2015-11-22 19:53:10', '2015-11-22 19:53:10'),
            array('06cbd1bd-4f0b-11e6-84b7-c8cbb8b4347f', 'Courchevel', 'ChIJbUa-FEN_iUcRPHPkLHCDnqY', 6.61409, 45.4203, null, null, null, null, '2015-11-22 17:41:23', '2015-11-22 17:41:23'),
            array('06e057a7-4f0b-11e6-84b7-c8cbb8b4347f', 'Thônes', 'ChIJbVP004Pyi0cRkJa65CqrCAQ', 6.324761, 45.882031, 45.936963, 45.829921, 6.4080671, 6.2577001, '2015-11-22 19:03:43', '2015-11-22 19:03:43'),
            array('06f70049-4f0b-11e6-84b7-c8cbb8b4347f', 'Bois-Guillaume', 'ChIJbxoumUXc4EcRorB5xGLmJ38', 1.1148305, 49.4688521, 49.493519, 49.452177, 1.145371, 1.093329, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('070be033-4f0b-11e6-84b7-c8cbb8b4347f', 'Fillinges', 'ChIJByIRfrdyjEcRUJ-65CqrCAQ', 6.34299, 46.159466, 46.193587, 46.1469799, 6.377583, 6.315278, '2015-11-22 17:36:09', '2015-11-22 17:36:09'),
            array('0722daa7-4f0b-11e6-84b7-c8cbb8b4347f', 'Cogolin', 'ChIJBYk_UgHJzhIRxRyt3_SJDtI', 6.532649, 43.251843, 43.273411, 43.2078289, 6.5937778, 6.4701828, '2016-01-19 14:01:24', '2016-01-19 14:01:24'),
            array('0736d32a-4f0b-11e6-84b7-c8cbb8b4347f', 'Montgeron', 'ChIJbyPrtKEK5kcR0EOLaMOCCwQ', 2.4604529, 48.703859, 48.72074, 48.6696199, 2.4931661, 2.4344861, '2015-11-22 20:41:30', '2015-11-22 20:41:30'),
            array('074b0644-4f0b-11e6-84b7-c8cbb8b4347f', 'Perrigny', 'ChIJc-n1dBbYjEcR8Umpr5ioJMo', 5.584129, 46.668307, 46.692611, 46.6581509, 5.629578, 5.5700321, '2015-11-22 18:15:07', '2015-11-22 18:15:07'),
            array('0760297a-4f0b-11e6-84b7-c8cbb8b4347f', 'Amiens', 'ChIJC3aL1xOE50cR4CBigT7xCgQ', 2.295753, 49.894067, 49.9502879, 49.8468359, 2.3457469, 2.223528, '2016-02-10 14:18:48', '2016-02-10 14:18:48'),
            array('07782893-4f0b-11e6-84b7-c8cbb8b4347f', 'Peymeinade', 'ChIJC4eY-FYmzBIRAJqX_aUZCAQ', 6.875682, 43.643116, 43.6513539, 43.6064049, 6.9022029, 6.8591919, '2015-11-22 18:16:32', '2015-11-22 18:16:32'),
            array('078ce3dc-4f0b-11e6-84b7-c8cbb8b4347f', 'Collias', 'ChIJc4Gfvd_LtRIRVjNMVT4EQJM', 4.478372, 43.952824, 43.980381, 43.918313, 4.5014219, 4.4422769, '2015-11-22 17:39:09', '2015-11-22 17:39:09'),
            array('07a31e1c-4f0b-11e6-84b7-c8cbb8b4347f', 'Annecy-le-Vieux', 'ChIJC5L-T5aPi0cRwEQ_IzW5nZ8', 6.1419499, 45.9192139, 45.9414069, 45.8999627, 6.2044191, 6.110881, '2015-11-22 17:50:12', '2015-11-22 17:50:12'),
            array('07ba25f4-4f0b-11e6-84b7-c8cbb8b4347f', 'Talence', 'ChIJC5nYpnAnVQ0RINEWSBdlBgQ', -0.588054, 44.802614, 44.8253819, 44.786829, -0.572613, -0.6111041, '2015-11-22 18:21:21', '2015-11-22 18:21:21'),
            array('07cecb77-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Pierre-de-Chartreuse', 'ChIJC6jW-U_4ikcR0Gi-5CqrCAQ', 5.815652, 45.341365, 45.382227, 45.281301, 5.879547, 5.707117, '2016-04-03 22:25:40', '2016-04-03 22:25:40'),
            array('07e3a062-4f0b-11e6-84b7-c8cbb8b4347f', 'Verbier', 'ChIJc6mm987PjkcRkYgnDZw-3v8', 7.2288752, 46.0960759, null, null, null, null, '2016-01-29 14:48:45', '2016-01-29 14:48:45'),
            array('07f7652c-4f0b-11e6-84b7-c8cbb8b4347f', 'Albi', 'ChIJC7eXebHCrRIR4PA7L5z2BgQ', 2.1486413, 43.9250853, 43.969493, 43.888488, 2.2118179, 2.0527828, '2016-02-11 11:17:59', '2016-02-11 11:17:59'),
            array('080b9573-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Castellet', 'ChIJC9O7g0gIyRIRkM6P_aUZCAQ', 5.776755, 43.203551, 43.269105, 43.15873, 5.8066729, 5.7012669, '2015-11-22 19:54:20', '2015-11-22 19:54:20'),
            array('081f5844-4f0b-11e6-84b7-c8cbb8b4347f', 'Osny', 'ChIJCaHVm1b05kcRAC-LaMOCCwQ', 2.062715, 49.0700239, 49.0864269, 49.0472469, 2.0956991, 2.0285391, '2015-11-22 19:13:13', '2015-11-22 19:13:13'),
            array('084d1a8b-4f0b-11e6-84b7-c8cbb8b4347f', 'Saumur', 'ChIJCbybo83wB0gRAIkNHlI3DQQ', -0.080893, 47.260135, 47.3167101, 47.2123031, -0.0092518, -0.1599739, '2016-01-20 16:40:28', '2016-01-20 16:40:28'),
            array('08607a45-4f0b-11e6-84b7-c8cbb8b4347f', 'Bouc-Bel-Air', 'ChIJCcXXG0OTyRIRoASX_aUZCAQ', 5.413875, 43.451152, 43.473952, 43.4145159, 5.4486889, 5.37493, '2015-11-22 19:12:40', '2015-11-22 19:12:40'),
            array('087457d3-4f0b-11e6-84b7-c8cbb8b4347f', 'Rhône', 'ChIJcf_obOGN9EcR0Cm55CqrCAM', 4.6108043, 45.7351456, 46.306502, 45.45413, 5.160109, 4.243647, '2016-03-09 12:39:21', '2016-03-09 12:39:21'),
            array('08872380-4f0b-11e6-84b7-c8cbb8b4347f', 'Villers-lès-Nancy', 'ChIJcfRkXEailEcRsOo6mrlfCgQ', 6.153496, 48.673218, 48.6805649, 48.640296, 6.1645921, 6.090488, '2015-11-22 18:18:55', '2015-11-22 18:18:55'),
            array('089be4dd-4f0b-11e6-84b7-c8cbb8b4347f', 'Joué-lès-Tours', 'ChIJcWDGIhfW_EcRyR4q_XnK6Us', 0.6613099, 47.351861, 47.375242, 47.299816, 0.699296, 0.609459, '2015-11-22 19:53:10', '2015-11-22 19:53:10'),
            array('08b05018-4f0b-11e6-84b7-c8cbb8b4347f', 'Manosque', 'ChIJCWtsurPNyxIREYBvF1QI1Rs', 5.790916, 43.835744, 43.8839439, 43.7747109, 5.8531579, 5.727, '2016-01-22 09:32:46', '2016-01-22 09:32:46'),
            array('08c3d0f4-4f0b-11e6-84b7-c8cbb8b4347f', 'Besançon', 'ChIJCxPu8PpijUcRMO0TszTOCQQ', 6.0240539, 47.237829, 47.3197439, 47.200687, 6.0836499, 5.940887, '2016-01-27 17:58:27', '2016-01-27 17:58:27'),
            array('08d75870-4f0b-11e6-84b7-c8cbb8b4347f', 'Roybon', 'ChIJCYPVxtnIikcRgG6-5CqrCAQ', 5.243804, 45.25865, 45.282599, 45.2044619, 5.3326031, 5.176703, '2015-11-22 18:22:47', '2015-11-22 18:22:47'),
            array('08eb707f-4f0b-11e6-84b7-c8cbb8b4347f', 'Pélissanne', 'ChIJd-7yaiL5yRIRQAGX_aUZCAQ', 5.150567, 43.631049, 43.6588569, 43.6011169, 5.197999, 5.127482, '2015-11-22 18:30:48', '2015-11-22 18:30:48'),
            array('08fff834-4f0b-11e6-84b7-c8cbb8b4347f', 'Fréjus', 'ChIJd05gmDSYzhIR8MyP_aUZCAQ', 6.737034, 43.433152, 43.533516, 43.3682689, 6.8964779, 6.6859385, '2015-11-22 19:47:14', '2015-11-22 19:47:14'),
            array('091509b2-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Petit-Quevilly', 'ChIJD0PEniHe4EcRFiNWweU6JT0', 1.054325, 49.429895, 49.4376691, 49.4099009, 1.0764431, 1.0379799, '2015-11-22 17:58:38', '2015-11-22 17:58:38'),
            array('09290b2d-4f0b-11e6-84b7-c8cbb8b4347f', 'Tignes Val Claret 2200', 'ChIJd5U3YbF0iUcRsRFqPUX9jLE', 6.900281, 45.455334, null, null, null, null, '2015-12-03 16:31:30', '2015-12-03 16:31:30'),
            array('093be81d-4f0b-11e6-84b7-c8cbb8b4347f', 'Paris', 'ChIJD7fiBh9u5kcRYJSMaMOCCwQ', 2.3522219, 48.856614, 48.9021449, 48.815573, 2.4699209, 2.225193, '2015-11-22 17:55:20', '2015-11-22 17:55:20'),
            array('094effea-4f0b-11e6-84b7-c8cbb8b4347f', 'Malakoff', 'ChIJd8fRiVlw5kcRmL51zu7Cmz0', 2.2977599, 48.817275, 48.825134, 48.808941, 2.3141281, 2.274473, '2015-11-22 18:37:34', '2015-11-22 18:37:34'),
            array('096226b3-4f0b-11e6-84b7-c8cbb8b4347f', 'Vezin-le-Coquet', 'ChIJD9VDxQ_hDkgRQKTkNs2lDAQ', -1.755908, 48.118465, 48.132609, 48.0993008, -1.723957, -1.781573, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('09766fa7-4f0b-11e6-84b7-c8cbb8b4347f', 'Sceaux', 'ChIJd9YzjLVw5kcROGRjsZYUBo8', 2.295092, 48.778016, 48.785396, 48.766604, 2.314236, 2.2782821, '2015-11-22 18:37:34', '2015-11-22 18:37:34'),
            array('098b69db-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Ouen', 'ChIJDak3HeRu5kcRkDqLaMOCCwQ', 2.333764, 48.911856, 48.9229134, 48.900438, 2.351965, 2.3142852, '2016-01-14 21:21:18', '2016-01-14 21:21:18'),
            array('099ef104-4f0b-11e6-84b7-c8cbb8b4347f', 'Chaligny', 'ChIJdank4G6hlEcRokeqIuQb068', 6.08439, 48.624955, 48.667612, 48.6166708, 6.128308, 6.0527391, '2015-11-22 18:18:55', '2015-11-22 18:18:55'),
            array('09b2c6c2-4f0b-11e6-84b7-c8cbb8b4347f', 'Rumilly', 'ChIJDRmWAoOdi0cRz7imTQai8R4', 5.942357, 45.867137, 45.8864269, 45.8243149, 5.9724481, 5.9101461, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('09c82b42-4f0b-11e6-84b7-c8cbb8b4347f', 'La Rochelle', 'ChIJdT0lyYNTAUgRQJbuYJLTBQQ', -1.151139, 46.160329, 46.1909488, 46.133163, -1.111052, -1.2414699, '2015-11-22 17:42:19', '2015-11-22 17:42:19'),
            array('09dbb886-4f0b-11e6-84b7-c8cbb8b4347f', 'Lagnieu', 'ChIJdUI73G5Oi0cRO77OJNFfmtg', 5.3486269, 45.903996, 45.9245629, 45.8480759, 5.392877, 5.286449, '2015-11-22 19:02:09', '2015-11-22 19:02:09'),
            array('09f3df04-4f0b-11e6-84b7-c8cbb8b4347f', 'Gardanne', 'ChIJDUKSKXuRyRIRAAOX_aUZCAQ', 5.4717363, 43.4525982, 43.489082, 43.427986, 5.5294779, 5.441847, '2016-01-11 15:01:56', '2016-01-11 15:01:56'),
            array('0a07cf65-4f0b-11e6-84b7-c8cbb8b4347f', 'Rencurel', 'ChIJDyXOI7GVikcRYG--5CqrCAQ', 5.472938, 45.104161, 45.168053, 45.0675339, 5.5166321, 5.433831, '2015-11-23 19:14:37', '2015-11-23 19:14:37'),
            array('0a1bf43e-4f0b-11e6-84b7-c8cbb8b4347f', 'Dijon', 'ChIJdZb974yd8kcR0FgUszTOCQQ', 5.04148, 47.322047, 47.377463, 47.286299, 5.101999, 4.9624431, '2015-11-22 19:50:11', '2015-11-22 19:50:11'),
            array('0a304441-4f0b-11e6-84b7-c8cbb8b4347f', 'Ski School Val Thorens', 'ChIJDZMrrIqGiUcRUENdCV9T9uE', 6.582323, 45.298173, null, null, null, null, '2016-01-29 17:29:24', '2016-01-29 17:29:24'),
            array('0a443e03-4f0b-11e6-84b7-c8cbb8b4347f', 'La Rosière', 'ChIJdZooPVJpiUcRsD0ogy2rCAo', 6.849464, 45.62729, null, null, null, null, '2015-12-04 10:17:18', '2015-12-04 10:17:18'),
            array('0a5839af-4f0b-11e6-84b7-c8cbb8b4347f', 'Senlis', 'ChIJE3YB99ow5kcRnjxrD7lAVn4', 2.583212, 49.205164, 49.258142, 49.1731579, 2.634096, 2.548635, '2015-11-22 18:09:40', '2015-11-22 18:09:40'),
            array('0a6ba4c9-4f0b-11e6-84b7-c8cbb8b4347f', '73440', 'ChIJe48WdkEqikcRMIbkQS6rCBw', 6.5078564, 45.3588933, 45.4736345, 45.2521613, 6.6243576, 6.3875667, '2015-11-22 15:31:54', '2015-11-22 15:31:54'),
            array('0a816efa-4f0b-11e6-84b7-c8cbb8b4347f', 'Courchevel Tourisme', 'ChIJE7AILaN4iUcRvHJ0g1Elucw', 6.63469, 45.414659, null, null, null, null, '2016-01-26 20:26:51', '2016-01-26 20:26:51'),
            array('0a9b4bd7-4f0b-11e6-84b7-c8cbb8b4347f', 'Avoriaz', 'ChIJeaU1B1KmjkcRALIogy2rCAo', 6.776343, 46.189683, null, null, null, null, '2016-02-04 15:20:58', '2016-02-04 15:20:58'),
            array('0ab5c1d6-4f0b-11e6-84b7-c8cbb8b4347f', 'Caluire-et-Cuire', 'ChIJEe9TWczq9EcRcCe75CqrCAQ', 4.842426, 45.79681, 45.8184469, 45.7787829, 4.879392, 4.8190031, '2016-02-29 09:34:10', '2016-02-29 09:34:10'),
            array('0ad1c5b4-4f0b-11e6-84b7-c8cbb8b4347f', 'Morangis', 'ChIJEQx9axl25kcRsEOLaMOCCwQ', 2.336018, 48.7022059, 48.7169229, 48.6875109, 2.355986, 2.316153, '2015-11-22 19:21:20', '2015-11-22 19:21:20'),
            array('0ae6784f-4f0b-11e6-84b7-c8cbb8b4347f', 'Chantilly', 'ChIJeR6gXf435kcRMEdkgT7xCgQ', 2.4687389, 49.19316, 49.2033149, 49.1566079, 2.5273531, 2.4478411, '2016-02-02 18:05:55', '2016-02-02 18:05:55'),
            array('0afa83b9-4f0b-11e6-84b7-c8cbb8b4347f', 'Tignes (Val d\'Isère)', 'ChIJERtK5rZ0iUcR4Xyjhu1lPJs', 6.897789, 45.4527384, null, null, null, null, '2015-11-22 18:03:47', '2015-11-22 18:03:47'),
            array('0b0f4bf8-4f0b-11e6-84b7-c8cbb8b4347f', 'Courchevel 1650', 'ChIJeS8FzaJ4iUcRMf39YtTLq-s', 6.6514027, 45.4173069, 45.4210517, 45.413691, 6.656815, 6.6476941, '2015-11-22 19:20:33', '2015-11-22 19:20:33'),
            array('0b23c7d5-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Maximin-la-Sainte-Baume', 'ChIJESJtP1RwyRIR147cbx1_bGM', 5.864004, 43.452497, 43.4975459, 43.400033, 5.9255519, 5.7950608, '2015-11-22 18:23:42', '2015-11-22 18:23:42'),
            array('0b37e701-4f0b-11e6-84b7-c8cbb8b4347f', 'Briançon', 'ChIJEUF0A2jkiUcRoKmX_aUZCAQ', 6.643179, 44.899416, 44.9298269, 44.8692259, 6.706599, 6.60071, '2015-11-22 17:43:58', '2015-11-22 17:43:58'),
            array('0b628378-4f0b-11e6-84b7-c8cbb8b4347f', 'Lille', 'ChIJEW4ls3nVwkcRYGNkgT7xCgQ', 3.057256, 50.62925, 50.661248, 50.6008871, 3.1263549, 2.967966, '2015-11-22 18:13:08', '2015-11-22 18:13:08'),
            array('0b777f9e-4f0b-11e6-84b7-c8cbb8b4347f', 'Gignac', 'ChIJEXMAgBRasRIR8MdqFiGIBwQ', 3.551364, 43.653241, 43.670671, 43.6125199, 3.620894, 3.5113359, '2016-04-25 14:59:35', '2016-04-25 14:59:35'),
            array('0b8b8a9a-4f0b-11e6-84b7-c8cbb8b4347f', 'Cabriès', 'ChIJey4yK2fsyRIRYASX_aUZCAQ', 5.37999, 43.441195, 43.488877, 43.417008, 5.3924519, 5.300954, '2015-11-22 18:23:42', '2015-11-22 18:23:42'),
            array('0ba14bd0-4f0b-11e6-84b7-c8cbb8b4347f', 'Sausset-les-Pins', 'ChIJEY9760beyRIREP-W_aUZCAQ', 5.11057, 43.332412, 43.3659869, 43.3278331, 5.143285, 5.088328, '2015-11-22 19:46:36', '2015-11-22 19:46:36'),
            array('0bb56677-4f0b-11e6-84b7-c8cbb8b4347f', 'Bourg-en-Bresse', 'ChIJEZNoFS1S80cR-4rzNZfW4cI', 5.2255007, 46.2051675, 46.230133, 46.1752829, 5.287206, 5.206617, '2015-11-22 19:14:52', '2015-11-22 19:14:52'),
            array('0bc89ffe-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Mandé', 'ChIJF_8YUI5y5kcRADiLaMOCCwQ', 2.4178639, 48.842832, 48.8493889, 48.833488, 2.4282971, 2.411257, '2015-11-22 19:44:06', '2015-11-22 19:44:06'),
            array('0bdb6ccc-4f0b-11e6-84b7-c8cbb8b4347f', 'Chatou', 'ChIJF0sdQQFj5kcRYEKMaMOCCwQ', 2.160098, 48.896391, 48.912095, 48.8807211, 2.1739964, 2.125994, '2015-11-22 18:36:33', '2015-11-22 18:36:33'),
            array('0bef850f-4f0b-11e6-84b7-c8cbb8b4347f', 'Monieux', 'ChIJf2TjdXxpyhIRMMKP_aUZCAQ', 5.359366, 44.067421, 44.097717, 44.006372, 5.3913219, 5.2987159, '2015-11-22 18:20:13', '2015-11-22 18:20:13'),
            array('0c040e3b-4f0b-11e6-84b7-c8cbb8b4347f', 'Valloire', 'ChIJF4N8YxIhikcRrjQUqhdSoP8', 6.428733, 45.165292, 45.22443, 45.051689, 6.51043, 6.3342409, '2015-12-24 15:42:58', '2015-12-24 15:42:58'),
            array('0c176042-4f0b-11e6-84b7-c8cbb8b4347f', 'Île-de-France', 'ChIJF4ymA8Th5UcRcCWLaMOCCwE', 2.6370411, 48.8499198, 49.2415039, 48.120081, 3.559007, 1.4461701, '2016-03-07 22:25:47', '2016-03-07 22:25:47'),
            array('0c2ade01-4f0b-11e6-84b7-c8cbb8b4347f', 'Grindelwald', 'ChIJF5LHGKuej0cRZS-XQg9cqr8', 8.0413962, 46.624164, 46.69286, 46.5464899, 8.14343, 7.93812, '2016-01-29 14:48:45', '2016-01-29 14:48:45'),
            array('0c3e77df-4f0b-11e6-84b7-c8cbb8b4347f', 'Cilaos', 'ChIJf6AXsxSdgiERQkhycJmMX9E', 55.4582381, -21.1460213, -21.0856564, -21.1950254, 55.5073177, 55.4036397, '2015-11-22 19:09:13', '2015-11-22 19:09:13'),
            array('0c5223b2-4f0b-11e6-84b7-c8cbb8b4347f', 'Montferrier-sur-Lez', 'ChIJF7K22iGpthIRgMRqFiGIBwQ', 3.859265, 43.671824, 43.687701, 43.647729, 3.8832549, 3.840993, '2015-11-22 18:17:27', '2015-11-22 18:17:27'),
            array('0c65d0fa-4f0b-11e6-84b7-c8cbb8b4347f', 'Pontoise', 'ChIJFbdRhm715kcR2Ommp1NXoCA', 2.100645, 49.050966, 49.0732959, 49.024271, 2.126987, 2.0722631, '2015-11-22 19:13:13', '2015-11-22 19:13:13'),
            array('0c796aef-4f0b-11e6-84b7-c8cbb8b4347f', 'Tarare', 'ChIJFd5-ISlk9EcR19sqY32TZE4', 4.433425, 45.89676, 45.9309919, 45.8831289, 4.464455, 4.395963, '2015-11-22 19:42:43', '2015-11-22 19:42:43'),
            array('0c8d356a-4f0b-11e6-84b7-c8cbb8b4347f', 'Murat', 'ChIJfeH0Oqvc90cRA-ybBRNA3Gg', 2.86973, 45.1106539, 45.1266169, 45.092187, 2.891221, 2.8333251, '2015-11-22 19:45:53', '2015-11-22 19:45:53'),
            array('0ca1196f-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Étienne', 'ChIJFeTPDf-r9UcRVldjh9uyMQY', 4.3871779, 45.439695, 45.476736, 45.371484, 4.488875, 4.2440541, '2015-12-09 10:47:43', '2015-12-09 10:47:43'),
            array('0cb589ba-4f0b-11e6-84b7-c8cbb8b4347f', 'Haute-Savoie', 'ChIJfRdqefQJjEcRICq55CqrCAM', 6.5389621, 46.1756788, 46.408243, 45.681659, 7.045065, 5.80502, '2015-12-02 15:31:44', '2015-12-02 15:31:44'),
            array('0cc92e26-4f0b-11e6-84b7-c8cbb8b4347f', 'Croissy-sur-Seine', 'ChIJfRXJ291i5kcRO0JboE0sMps', 2.142746, 48.878426, 48.890777, 48.870509, 2.1576098, 2.1112039, '2015-11-22 18:36:33', '2015-11-22 18:36:33'),
            array('0cdd0c87-4f0b-11e6-84b7-c8cbb8b4347f', 'Aime', 'ChIJfZT_lLF8iUcRkH9YS6Jsfwo', 6.652126, 45.555982, 45.6199879, 45.4911049, 6.6692291, 6.5616761, '2015-11-22 18:59:43', '2015-11-22 18:59:43'),
            array('0cf0946c-4f0b-11e6-84b7-c8cbb8b4347f', 'Napapijri Val Thorens', 'ChIJG_dqt2GGiUcR0BgqokyshR4', 6.5844174, 45.2963775, null, null, null, null, '2015-11-22 19:20:33', '2015-11-22 19:20:33'),
            array('0d04ab3c-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Contamines-Montjoie', 'ChIJG3CQYCtaiUcR3YLzCNWXivs', 6.726553, 45.822746, 45.848049, 45.723038, 6.813118, 6.6591691, '2015-11-22 18:20:13', '2015-11-22 18:20:13'),
            array('0d1784fe-4f0b-11e6-84b7-c8cbb8b4347f', 'Varces-Allières-et-Risset', 'ChIJg5riUh-MikcRQGS-5CqrCAQ', 5.6826762, 45.0905796, 45.111527, 45.065391, 5.7253101, 5.6143381, '2015-11-22 18:56:52', '2015-11-22 18:56:52'),
            array('0d2a40c5-4f0b-11e6-84b7-c8cbb8b4347f', 'Méribel', 'ChIJG7K6NjmAiUcRUIyElH2rvUA', 6.566576, 45.396839, null, null, null, null, '2015-11-22 17:43:18', '2015-11-22 17:43:18'),
            array('0d3d2e4b-4f0b-11e6-84b7-c8cbb8b4347f', 'Fromentine', 'ChIJGc8iolsbBUgRcrerZFM3DSY', -2.140027, 46.889175, null, null, null, null, '2015-12-03 12:12:34', '2015-12-03 12:12:34'),
            array('0d52d6cb-4f0b-11e6-84b7-c8cbb8b4347f', 'Montvalezan', 'ChIJGcE9c-9riUcRsK-65CqrCAQ', 6.846731, 45.611792, 45.664146, 45.603222, 6.91686, 6.828437, '2015-11-22 20:34:33', '2015-11-22 20:34:33'),
            array('0d66e900-4f0b-11e6-84b7-c8cbb8b4347f', 'Bordeaux', 'ChIJgcpR9-gnVQ0RiXo5ewOGY3k', -0.57918, 44.837789, 44.9167039, 44.810752, -0.533309, -0.638973, '2015-11-22 17:48:31', '2015-11-22 17:48:31'),
            array('0d7a4175-4f0b-11e6-84b7-c8cbb8b4347f', '6th arrondissement of Paris', 'ChIJGcU48NBx5kcRoBqUaMOCCwU', 2.3354223, 48.8488576, 48.8593331, 48.8396631, 2.344615, 2.3167041, '2015-11-22 19:21:20', '2015-11-22 19:21:20'),
            array('0d8fee32-4f0b-11e6-84b7-c8cbb8b4347f', 'Castres', 'ChIJgcUlS94RrhIRIO07L5z2BgQ', 2.241295, 43.606214, 43.6703769, 43.5560261, 2.333271, 2.15636, '2016-02-08 09:53:08', '2016-02-08 09:53:08'),
            array('0da44b5b-4f0b-11e6-84b7-c8cbb8b4347f', 'Servoz', 'ChIJGdFg-1hRiUcRiFeXKkiAByc', 6.7678249, 45.9400788, 45.964191, 45.927417, 6.824629, 6.74268, '2015-11-22 19:17:53', '2015-11-22 19:17:53'),
            array('0db87d5d-4f0b-11e6-84b7-c8cbb8b4347f', 'Houilles', 'ChIJGSNcMPFj5kcRYD6MaMOCCwQ', 2.18888, 48.926916, 48.9371579, 48.915989, 2.205152, 2.1679641, '2016-02-28 22:11:08', '2016-02-28 22:11:08'),
            array('0dcd34ef-4f0b-11e6-84b7-c8cbb8b4347f', '13th arrondissement', 'ChIJgwVOTCdy5kcREBuUaMOCCwU', 2.359204, 48.830759, 48.8449649, 48.815573, 2.390053, 2.3411081, '2015-11-22 17:55:20', '2015-11-22 17:55:20'),
            array('0de2cb0c-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Arcs', 'ChIJgwxVkQ9viUcRwq995YhoKn8', 6.8296771, 45.5722522, null, null, null, null, '2015-11-22 16:22:57', '2015-11-22 16:22:57'),
            array('0df630a9-4f0b-11e6-84b7-c8cbb8b4347f', 'Lognes', 'ChIJgxnjdF8F5kcRMFaMaMOCCwQ', 2.6327379, 48.836571, 48.8453839, 48.819581, 2.657896, 2.6151761, '2015-11-22 18:57:43', '2015-11-22 18:57:43'),
            array('0e09b77b-4f0b-11e6-84b7-c8cbb8b4347f', 'Joinville-le-Pont', 'ChIJgzvjTA8N5kcRji8S24tNyeY', 2.472043, 48.821267, 48.8308671, 48.809299, 2.4825311, 2.454482, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('0e1d8b67-4f0b-11e6-84b7-c8cbb8b4347f', 'Appoigny', 'ChIJh_74OABO7kcR0GwNszTOCQQ', 3.526249, 47.876976, 47.896056, 47.837691, 3.557327, 3.4839481, '2015-11-22 18:15:07', '2015-11-22 18:15:07'),
            array('0e323fad-4f0b-11e6-84b7-c8cbb8b4347f', 'Val Thorens', 'ChIJh_ePD2CGiUcREFEogy2rCAo', 6.582435, 45.2981742, null, null, null, null, '2015-11-22 16:30:59', '2015-11-22 16:30:59'),
            array('0e7a1512-4f0b-11e6-84b7-c8cbb8b4347f', 'La Tania', 'ChIJH-NHowt_iUcRwRlz4yurCCY', 6.5949887, 45.4317306, null, null, null, null, '2016-02-18 06:54:41', '2016-02-18 06:54:41'),
            array('0e9e7b76-4f0b-11e6-84b7-c8cbb8b4347f', 'Lunel', 'ChIJH1yz-ImethIRCjfxtGQXr-8', 4.135366, 43.67445, 43.7148749, 43.64116, 4.1666059, 4.0991988, '2015-11-22 17:59:48', '2015-11-22 17:59:48'),
            array('0eb2715d-4f0b-11e6-84b7-c8cbb8b4347f', 'Essonne', 'ChIJH3mvlvjP5UcRMCuLaMOCCwM', 2.1569416, 48.4585698, 48.7761319, 48.2845559, 2.5856331, 1.9145131, '2016-01-18 12:39:54', '2016-01-18 12:39:54'),
            array('0ec626f5-4f0b-11e6-84b7-c8cbb8b4347f', 'Seraincourt', 'ChIJH6Wx2cfs5kcRiQrzz3Wr8n0', 1.865539, 49.035358, 49.066294, 49.013838, 1.908443, 1.851058, '2015-11-22 20:30:01', '2015-11-22 20:30:01'),
            array('0eda61b4-4f0b-11e6-84b7-c8cbb8b4347f', 'Cavaillon', 'ChIJHcPgI4z3tRIRh7iXQUVBPXE', 5.0407814, 43.8366045, 43.890607, 43.806495, 5.0930229, 4.973556, '2016-03-23 14:41:40', '2016-03-23 14:41:40'),
            array('0eee693b-4f0b-11e6-84b7-c8cbb8b4347f', 'Carpentras', 'ChIJHdklSv6JtRIRMMWP_aUZCAQ', 5.048722, 44.0555639, 44.1033929, 44.022331, 5.10199, 5.0148979, '2016-02-10 14:23:50', '2016-02-10 14:23:50'),
            array('0f034fe7-4f0b-11e6-84b7-c8cbb8b4347f', 'Montreuil', 'ChIJhe8OU0Vt5kcR8FvEsG18qqM', 2.448451, 48.863812, 48.8787489, 48.848743, 2.4828051, 2.4152961, '2015-11-22 18:15:32', '2015-11-22 18:15:32'),
            array('0f182513-4f0b-11e6-84b7-c8cbb8b4347f', 'Sainte-Foy-lès-Lyon', 'ChIJHeCBtPrr9EcRAB275CqrCAQ', 4.804827, 45.735237, 45.751708, 45.720798, 4.8134511, 4.7717491, '2015-11-22 18:16:35', '2015-11-22 18:16:35'),
            array('0f2c6242-4f0b-11e6-84b7-c8cbb8b4347f', 'Wasquehal', 'ChIJheNTNKIpw0cRgFFkgT7xCgQ', 3.130782, 50.669276, 50.6942761, 50.650883, 3.1498251, 3.106705, '2015-11-22 18:13:08', '2015-11-22 18:13:08'),
            array('0f41d9af-4f0b-11e6-84b7-c8cbb8b4347f', 'Vienne', 'ChIJhfn1JAjf9EcRwwNatGC74cQ', 4.874339, 45.525587, 45.557463, 45.4867008, 4.9234099, 4.837252, '2015-11-22 18:16:35', '2015-11-22 18:16:35'),
            array('0f56c9c5-4f0b-11e6-84b7-c8cbb8b4347f', 'Castelnau-le-Lez', 'ChIJHQWWl_ClthIRgMtqFiGIBwQ', 3.8975051, 43.6329827, 43.655996, 43.6184099, 3.936763, 3.8857889, '2015-11-22 17:59:48', '2015-11-22 17:59:48'),
            array('0f6bd1fc-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Mesnil-le-Roi', 'ChIJhRf4xhVi5kcRIDyMaMOCCwQ', 2.123343, 48.924857, 48.9436169, 48.9064339, 2.1418542, 2.104234, '2016-02-28 22:11:08', '2016-02-28 22:11:08'),
            array('0f818518-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Mesnil-Esnard', 'ChIJhROrHiTZ4EcRA__oShbY6wI', 1.1464319, 49.412267, 49.427603, 49.396962, 1.1666001, 1.126156, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('0f96f66b-4f0b-11e6-84b7-c8cbb8b4347f', 'Péone', 'ChIJHS9hoWBXzBIREJqX_aUZCAQ', 6.906817, 44.116273, 44.1903769, 44.0761319, 6.9722289, 6.87778, '2015-11-22 19:05:32', '2015-11-22 19:05:32'),
            array('0fb23981-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Germain-en-Laye', 'ChIJhUmS2AZi5kcRQDiMaMOCCwQ', 2.093761, 48.898908, 48.9872419, 48.8839029, 2.17325, 2.039569, '2015-11-22 18:36:33', '2015-11-22 18:36:33'),
            array('0fc7fe33-4f0b-11e6-84b7-c8cbb8b4347f', 'Chamrousse', 'ChIJhV28yxpnikcRkGG-5CqrCAQ', 5.8746192, 45.1106963, 45.141334, 45.0940099, 5.931196, 5.870587, '2015-11-22 15:47:54', '2015-11-22 15:47:54'),
            array('0fdd6a2d-4f0b-11e6-84b7-c8cbb8b4347f', 'Franconville', 'ChIJHwFYsBln5kcR4DKLaMOCCwQ', 2.229632, 48.986011, 49.0035299, 48.972243, 2.244073, 2.202899, '2016-01-06 14:47:45', '2016-01-06 14:47:45'),
            array('0ff2cc72-4f0b-11e6-84b7-c8cbb8b4347f', 'Ramonville', 'ChIJhWuASym8rhIR2Qp1dob8hgM', 1.4764009, 43.5558449, null, null, null, null, '2016-03-19 15:04:14', '2016-03-19 15:04:14'),
            array('10085714-4f0b-11e6-84b7-c8cbb8b4347f', 'Lunéville', 'ChIJHxnG-b-AlEcR4Pk6mrlfCgQ', 6.492339, 48.592237, 48.62065, 48.5718529, 6.564936, 6.455448, '2015-11-22 20:39:11', '2015-11-22 20:39:11'),
            array('101dbb85-4f0b-11e6-84b7-c8cbb8b4347f', 'Rennes', 'ChIJhZDWpy_eDkgRMKvkNs2lDAQ', -1.6777926, 48.117266, 48.15497, 48.0768609, -1.6243669, -1.752542, '2015-11-22 18:11:45', '2015-11-22 18:11:45'),
            array('10329c3b-4f0b-11e6-84b7-c8cbb8b4347f', 'Bonneuil-sur-Marne', 'ChIJHzIi3H8L5kcR0DmLaMOCCwQ', 2.487765, 48.774755, 48.7875899, 48.756722, 2.514443, 2.471571, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('10471792-4f0b-11e6-84b7-c8cbb8b4347f', 'Ceyzériat', 'ChIJI_80J-usjEcRkBTC5CqrCAQ', 5.322114, 46.179414, 46.1981631, 46.1662039, 5.3448051, 5.293369, '2015-11-22 19:14:52', '2015-11-22 19:14:52'),
            array('105b0eda-4f0b-11e6-84b7-c8cbb8b4347f', 'Pacé', 'ChIJi-2-LK3mDkgR8oZGAhjoUQc', -1.77276, 48.146698, 48.1873929, 48.1195559, -1.7234859, -1.825394, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('106f0606-4f0b-11e6-84b7-c8cbb8b4347f', 'La Colle-sur-Loup', 'ChIJI-HWxNDSzRIRfyZGDTo61PE', 7.10342, 43.686732, 43.70547, 43.668285, 7.1237079, 7.0712909, '2015-11-22 20:43:14', '2015-11-22 20:43:14'),
            array('108294a0-4f0b-11e6-84b7-c8cbb8b4347f', 'Chartres', 'ChIJi-HxJEQM5EcRKe3HU0iIY3g', 1.489012, 48.443854, 48.46892, 48.427181, 1.54965, 1.459606, '2016-02-08 10:06:22', '2016-02-08 10:06:22'),
            array('1095f6bf-4f0b-11e6-84b7-c8cbb8b4347f', 'Bruges', 'ChIJi-RvQyrWVA0RUOwWSBdlBgQ', -0.612747, 44.881793, 44.9094999, 44.8691819, -0.5760459, -0.6297411, '2016-04-09 16:04:41', '2016-04-09 16:04:41'),
            array('10aa4ed0-4f0b-11e6-84b7-c8cbb8b4347f', 'Courchevel 1850', 'ChIJI2jAVqx4iUcRpZ4aDI6vGAw', 6.634407, 45.415284, null, null, null, null, '2015-11-22 15:31:54', '2015-11-22 15:31:54'),
            array('10c04c7d-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Houches', 'ChIJI3AKp1JQiUcRcJ665CqrCAQ', 6.798735, 45.890388, 45.94121, 45.8425889, 6.855464, 6.742666, '2015-11-22 20:35:49', '2015-11-22 20:35:49'),
            array('10d876b5-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Briac-sur-Mer', 'ChIJi3l3Nit8DkgREKrkNs2lDAQ', -2.132691, 48.619354, 48.6410799, 48.593277, -2.0980199, -2.1620259, '2015-11-22 19:01:39', '2015-11-22 19:01:39'),
            array('10f39e49-4f0b-11e6-84b7-c8cbb8b4347f', 'Lambersart', 'ChIJI63vhKEqw0cRATEMgKgR65w', 3.026676, 50.652468, 50.672268, 50.635425, 3.0476549, 3.0023851, '2015-11-22 19:22:16', '2015-11-22 19:22:16'),
            array('110e6421-4f0b-11e6-84b7-c8cbb8b4347f', 'Vaucresson', 'ChIJI6Z20JR85kcRwDyLaMOCCwQ', 2.1624469, 48.84389, 48.851658, 48.8214019, 2.182602, 2.145702, '2015-11-22 20:30:01', '2015-11-22 20:30:01'),
            array('1122c916-4f0b-11e6-84b7-c8cbb8b4347f', 'Carcassonne', 'ChIJi70WATksrhIRxxShdzvfj-I', 2.353663, 43.212161, 43.244357, 43.1712458, 2.43646, 2.261701, '2016-01-15 15:13:16', '2016-01-15 15:13:16'),
            array('11367c2f-4f0b-11e6-84b7-c8cbb8b4347f', 'Laneuville-au-Pont', 'ChIJibDPUYWH60cRCeAcU--YpLg', 4.858785, 48.629123, 48.640045, 48.6138781, 4.886213, 4.8483431, '2015-11-22 17:51:57', '2015-11-22 17:51:57'),
            array('114ae0e0-4f0b-11e6-84b7-c8cbb8b4347f', 'Cluses', 'ChIJicNpE94GjEcRuiD-B6wY98Y', 6.580582, 46.06039, 46.08459, 46.040365, 6.608081, 6.5468469, '2015-11-22 20:51:54', '2015-11-22 20:51:54'),
            array('115efc63-4f0b-11e6-84b7-c8cbb8b4347f', 'Arolla', 'ChIJicRWEesrj0cRtOrHrt5ZUYc', 7.4817905, 46.0233657, null, null, null, null, '2016-01-29 14:48:45', '2016-01-29 14:48:45'),
            array('1173e21a-4f0b-11e6-84b7-c8cbb8b4347f', '01000', 'ChIJId1ck9JT80cR0CfkQS6rCBw', 5.245281, 46.2135885, 46.2318981, 46.1753247, 5.2874553, 5.1529513, '2016-01-18 18:06:39', '2016-01-18 18:06:39'),
            array('118776d9-4f0b-11e6-84b7-c8cbb8b4347f', 'Landéda', 'ChIJifoCC1WgFkgR0GrlNs2lDAQ', -4.572328, 48.587021, 48.6151379, 48.5740611, -4.5276839, -4.6374958, '2015-11-22 19:14:11', '2015-11-22 19:14:11'),
            array('119ba475-4f0b-11e6-84b7-c8cbb8b4347f', 'Narbonne', 'ChIJifsASUGrsRIRoCNtFiGIBwQ', 3.003078, 43.184277, 43.2377749, 43.0610709, 3.184663, 2.8823959, '2015-12-04 10:19:50', '2015-12-04 10:19:50'),
            array('11b1b594-4f0b-11e6-84b7-c8cbb8b4347f', 'Sens', 'ChIJIR6uWE8F70cRMFcNszTOCQQ', 3.28268, 48.20065, 48.230078, 48.160098, 3.347948, 3.25982, '2015-11-22 18:05:44', '2015-11-22 18:05:44'),
            array('11c54c76-4f0b-11e6-84b7-c8cbb8b4347f', 'La Clusaz', 'ChIJiRJZ7nHwi0cRAKK65CqrCAQ', 6.423353, 45.904427, 45.9352189, 45.861328, 6.530963, 6.399856, '2015-11-22 15:31:54', '2015-11-22 15:31:54'),
            array('11d9c7fe-4f0b-11e6-84b7-c8cbb8b4347f', 'Allevard', 'ChIJIwL198FLikcRgIK-5CqrCAQ', 6.075192, 45.394486, 45.419538, 45.3323799, 6.1947641, 6.0422751, '2016-01-26 20:26:51', '2016-01-26 20:26:51'),
            array('11ee0106-4f0b-11e6-84b7-c8cbb8b4347f', 'Bernin', 'ChIJIx64o6lZikcRgIC-5CqrCAQ', 5.8664059, 45.269575, 45.2859009, 45.2413009, 5.889782, 5.835124, '2015-11-22 18:14:21', '2015-11-22 18:14:21'),
            array('12027434-4f0b-11e6-84b7-c8cbb8b4347f', 'Hauts-de-Seine', 'ChIJIX9UJN165kcRQCuLaMOCCwM', 2.2188068, 48.828508, 48.950193, 48.729351, 2.336461, 2.145702, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('1216dd94-4f0b-11e6-84b7-c8cbb8b4347f', 'Villeneuve-la-Garenne', 'ChIJixTS4Cxp5kcRfLa8XEMDoxs', 2.324789, 48.936616, 48.9475873, 48.92295, 2.336461, 2.309294, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('122a7336-4f0b-11e6-84b7-c8cbb8b4347f', 'Rocbaron', 'ChIJiYQnPJQ_yRIR6pKMu4FBUNk', 6.083557, 43.305978, 43.333627, 43.2787889, 6.1234859, 6.050398, '2015-11-22 17:49:02', '2015-11-22 17:49:02'),
            array('123ec597-4f0b-11e6-84b7-c8cbb8b4347f', 'Neuilly-Plaisance', 'ChIJiz4ClWQS5kcRUDuLaMOCCwQ', 2.509142, 48.865205, 48.8780689, 48.8500619, 2.524417, 2.4951251, '2015-11-22 18:57:43', '2015-11-22 18:57:43'),
            array('1252e541-4f0b-11e6-84b7-c8cbb8b4347f', 'Plougastel-Daoulas', 'ChIJj-lFZT23FkgReosqLgTtspM', -4.369108, 48.374051, 48.4028749, 48.322774, -4.318998, -4.463311, '2015-11-22 19:14:11', '2015-11-22 19:14:11'),
            array('1266a8c1-4f0b-11e6-84b7-c8cbb8b4347f', 'L\'Arbresle', 'ChIJJ0ldB2yL9EcR4Ci75CqrCAQ', 4.614934, 45.834999, 45.846807, 45.82264, 4.6252911, 4.595725, '2015-11-22 19:42:43', '2015-11-22 19:42:43'),
            array('1279bcc6-4f0b-11e6-84b7-c8cbb8b4347f', 'Roquebrune-sur-Argens', 'ChIJJ0ZWL0O7zhIREMqP_aUZCAQ', 6.637682, 43.443408, 43.51696, 43.339333, 6.719535, 6.585574, '2015-11-22 19:47:14', '2015-11-22 19:47:14'),
            array('128f536f-4f0b-11e6-84b7-c8cbb8b4347f', 'Charavines', 'ChIJj1nv8YvgikcRTw7WncVrdP4', 5.515649, 45.428551, 45.448152, 45.398067, 5.538152, 5.4950781, '2016-05-01 16:24:28', '2016-05-01 16:24:28'),
            array('12a407c3-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Kremlin-Bicêtre', 'ChIJJ2GTmHtx5kcR4DiLaMOCCwQ', 2.356972, 48.810108, 48.816379, 48.8010841, 2.368172, 2.343982, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('12b85f46-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Orres', 'ChIJJ2qizdiyikcR0ZyC3xtwPPk', 5.1078804, 45.0711929, 45.073085, 45.0693262, 5.1124084, 5.1032949, '2015-11-22 16:15:28', '2015-11-22 16:15:28'),
            array('12cd1e28-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Cloud', 'ChIJj45beSR75kcRgs9gsAoHoUw', 2.208115, 48.847647, 48.861685, 48.8263418, 2.224505, 2.180731, '2016-03-17 20:35:34', '2016-03-17 20:35:34'),
            array('12e44d74-4f0b-11e6-84b7-c8cbb8b4347f', 'Noisy-le-Roi', 'ChIJJ6JPQcaH5kcRtBDnVcMCppw', 2.064579, 48.843815, 48.8637899, 48.8293819, 2.0732111, 2.0351821, '2015-11-22 20:30:01', '2015-11-22 20:30:01'),
            array('12f846dc-4f0b-11e6-84b7-c8cbb8b4347f', 'Livry-Gargan', 'ChIJJ9K8GuwT5kcRgDuLaMOCCwQ', 2.536118, 48.91923, 48.9352959, 48.901485, 2.564405, 2.5057671, '2016-03-17 20:35:34', '2016-03-17 20:35:34'),
            array('130ba53d-4f0b-11e6-84b7-c8cbb8b4347f', 'Palavas-les-Flots', 'ChIJJTdopfS6thIRWpPx6V7YDrk', 3.933493, 43.528906, 43.547554, 43.5170401, 3.970434, 3.898753, '2015-11-23 21:20:36', '2015-11-23 21:20:36'),
            array('132042e6-4f0b-11e6-84b7-c8cbb8b4347f', 'Praz-sur-Arly', 'ChIJJWjTHG3ji0cRxM3yTRgbZIE', 6.571761, 45.837751, 45.8635329, 45.792456, 6.6366521, 6.5350761, '2015-11-22 19:52:07', '2015-11-22 19:52:07'),
            array('13344e72-4f0b-11e6-84b7-c8cbb8b4347f', 'Chapareillan', 'ChIJjwOZXr6si0cRQH6-5CqrCAQ', 5.9493458, 45.4554685, 45.4923409, 45.4323619, 6.011148, 5.903972, '2015-11-22 16:29:11', '2015-11-22 16:29:11'),
            array('1349162c-4f0b-11e6-84b7-c8cbb8b4347f', 'Belle Plagne', 'ChIJjWRIGk96iUcRklnaFPrqUXg', 6.706905, 45.506459, null, null, null, null, '2015-11-22 16:17:01', '2015-11-22 16:17:01'),
            array('135e10d3-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Raincy', 'ChIJJxLIerET5kcRLlKyL6di8UI', 2.5231119, 48.897386, 48.903861, 48.8878139, 2.53253, 2.5066061, '2016-03-17 20:35:34', '2016-03-17 20:35:34'),
            array('13735af3-4f0b-11e6-84b7-c8cbb8b4347f', 'Marly', 'ChIJJXQYbp3clEcRQJ86mrlfCgQ', 6.154795, 49.059828, 49.09023, 49.045881, 6.181434, 6.126548, '2015-11-22 19:12:11', '2015-11-22 19:12:11'),
            array('138b3900-4f0b-11e6-84b7-c8cbb8b4347f', 'Thonon-les-Bains', 'ChIJk_8GcYk-jEcRgJa65CqrCAQ', 6.477635, 46.373565, 46.4048388, 46.3440759, 6.515788, 6.4394211, '2016-01-27 16:56:23', '2016-01-27 16:56:23'),
            array('13a44e0f-4f0b-11e6-84b7-c8cbb8b4347f', 'Val-d\'Isère', 'ChIJk_tf_QkJiUcRMKi65CqrCAQ', 6.980226, 45.448034, 45.4798028, 45.3667849, 7.114734, 6.914812, '2015-11-22 17:41:23', '2015-11-22 17:41:23'),
            array('13b92bc9-4f0b-11e6-84b7-c8cbb8b4347f', 'Draveil', 'ChIJK0PEWOnf5UcRQEeLaMOCCwQ', 2.408154, 48.685388, 48.7014834, 48.6536959, 2.463786, 2.3858981, '2015-11-22 20:41:30', '2015-11-22 20:41:30'),
            array('13cf8b86-4f0b-11e6-84b7-c8cbb8b4347f', 'Serre Chevalier', 'ChIJK1sdh6T7iUcRN-kwOrEb1kY', 6.549748, 44.910829, null, null, null, null, '2015-11-22 16:14:32', '2015-11-22 16:14:32'),
            array('13e3ae3f-4f0b-11e6-84b7-c8cbb8b4347f', 'Brest', 'ChIJk1uS2eG7FkgRqzCcF1iDSMY', -4.486076, 48.390394, 48.459562, 48.3572541, -4.4302621, -4.568939, '2015-11-22 19:14:11', '2015-11-22 19:14:11'),
            array('13f97888-4f0b-11e6-84b7-c8cbb8b4347f', 'Sainte-Foy-Tarentaise', 'ChIJK3LQ-ydtiUcRcKy65CqrCAQ', 6.883265, 45.590821, 45.6550513, 45.5049336, 7.0048005, 6.844596, '2015-11-22 15:25:12', '2015-11-22 15:25:12'),
            array('141341a3-4f0b-11e6-84b7-c8cbb8b4347f', 'Rueil-Malmaison', 'ChIJK4enU1pj5kcRID2LaMOCCwQ', 2.17693, 48.882767, 48.8955683, 48.8476419, 2.213394, 2.1481721, '2015-11-22 18:36:33', '2015-11-22 18:36:33'),
            array('14286680-4f0b-11e6-84b7-c8cbb8b4347f', 'Sartrouville', 'ChIJK6JaBV9h5kcR0JaGGAOds1M', 2.158431, 48.941106, 48.95456, 48.920284, 2.206251, 2.143001, '2016-02-28 22:11:08', '2016-02-28 22:11:08'),
            array('144343f0-4f0b-11e6-84b7-c8cbb8b4347f', 'Peisey-Vallandry', 'ChIJk7pbPP1viUcRERTm2Jh0X_w', 6.761941, 45.553782, null, null, null, null, '2015-12-09 09:16:20', '2015-12-09 09:16:20'),
            array('1457c9ac-4f0b-11e6-84b7-c8cbb8b4347f', 'Pontault-Combault', 'ChIJk7s5zhIP5kcRMaBF4daBy7Q', 2.607598, 48.801255, 48.8159598, 48.7611059, 2.649238, 2.5854051, '2015-11-22 18:56:12', '2015-11-22 18:56:12'),
            array('146b3077-4f0b-11e6-84b7-c8cbb8b4347f', '75000', 'ChIJk7Y8YnRu5kcRlhLpeveeOBA', 2.3642198, 48.8785419, null, null, null, null, '2016-01-20 14:57:32', '2016-01-20 14:57:32'),
            array('1481443c-4f0b-11e6-84b7-c8cbb8b4347f', 'Chartreuse', 'ChIJK8ft4GL0ikcRO8SJmEsWeHU', 5.7245394, 45.1960379, 45.1963564, 45.1954033, 5.7251098, 5.7241901, '2015-11-22 16:36:04', '2015-11-22 16:36:04'),
            array('14965fef-4f0b-11e6-84b7-c8cbb8b4347f', 'Vannes', 'ChIJkeJ9w4QeEEgREz0jezBY87k', -2.760847, 47.658236, 47.6945089, 47.620816, -2.681761, -2.815186, '2016-01-19 18:42:43', '2016-01-19 18:42:43'),
            array('14cc0c68-4f0b-11e6-84b7-c8cbb8b4347f', 'Mauriac', 'ChIJKSgBP2AX-EcR4Mrqy688CQQ', 2.3366114, 45.2199787, 45.252837, 45.188933, 2.373727, 2.268017, '2015-11-22 19:45:53', '2015-11-22 19:45:53'),
            array('14dfa53c-4f0b-11e6-84b7-c8cbb8b4347f', 'Castanet-Tolosan', 'ChIJKTtwhkq-rhIRR8Br7aJkHkg', 1.499573, 43.51563, 43.527382, 43.4893, 1.533969, 1.4797569, '2016-03-19 15:04:14', '2016-03-19 15:04:14'),
            array('14f43534-4f0b-11e6-84b7-c8cbb8b4347f', 'Magny-Cours', 'ChIJkVpFUI5Q8EcR4IIQszTOCQQ', 3.15205, 46.883895, 46.9204169, 46.860727, 3.2123529, 3.0999641, '2015-11-22 19:50:11', '2015-11-22 19:50:11'),
            array('1507ebf9-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Bouscat', 'ChIJKwXtruHXVA0RsOwWSBdlBgQ', -0.59944, 44.865164, 44.876744, 44.853158, -0.575697, -0.63113, '2015-11-22 20:36:34', '2015-11-22 20:36:34'),
            array('151cdd36-4f0b-11e6-84b7-c8cbb8b4347f', 'Asnières-sur-Seine', 'ChIJKxuhrEJv5kcRwD6LaMOCCwQ', 2.285369, 48.914155, 48.9336049, 48.9027867, 2.3214665, 2.2649251, '2015-11-22 18:11:55', '2015-11-22 18:11:55'),
            array('152ffd4b-4f0b-11e6-84b7-c8cbb8b4347f', 'Fontenay-sous-Bois', 'ChIJkyBwzV4N5kcRQDmLaMOCCwQ', 2.475907, 48.851542, 48.861484, 48.8391259, 2.499984, 2.4471351, '2015-11-22 18:57:43', '2015-11-22 18:57:43'),
            array('154386dd-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Marécottes', 'ChIJkZKRwhy3jkcRzEhZNpKuASc', 7.0090893, 46.1124993, null, null, null, null, '2016-01-29 14:48:45', '2016-01-29 14:48:45'),
            array('15586d16-4f0b-11e6-84b7-c8cbb8b4347f', 'Reignier', 'ChIJl-TqsOlzjEcRdJ4YjyWZWwY', 6.270228, 46.13692, null, null, null, null, '2015-11-22 17:36:09', '2015-11-22 17:36:09'),
            array('156beda5-4f0b-11e6-84b7-c8cbb8b4347f', 'Sainte-Anne', 'ChIJl03jb5MjQIwR76zhJyIXSY8', -60.8493059, 14.4408054, 14.4741802, 14.3886471, -60.8141328, -60.89411, '2015-11-22 17:39:57', '2015-11-22 17:39:57'),
            array('15802f07-4f0b-11e6-84b7-c8cbb8b4347f', '13170', 'ChIJl0xPDM3ryRIR0Ma2UKkZCBw', 5.3207069, 43.4083231, 43.4330026, 43.3682437, 5.3793825, 5.2650943, '2016-01-19 14:43:45', '2016-01-19 14:43:45'),
            array('159445de-4f0b-11e6-84b7-c8cbb8b4347f', 'Orelle', 'ChIJl1aLA1yJiUcRoK665CqrCAQ', 6.534071, 45.210032, 45.266858, 45.113824, 6.6119071, 6.5121131, '2015-11-22 19:50:53', '2015-11-22 19:50:53'),
            array('15a931fd-4f0b-11e6-84b7-c8cbb8b4347f', 'Champigny-sur-Marne', 'ChIJL1welrQN5kcRp8GFVnUWV-o', 2.515556, 48.817049, 48.8315189, 48.803164, 2.5654611, 2.475669, '2015-11-22 18:56:12', '2015-11-22 18:56:12'),
            array('15bd2729-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Raphaël', 'ChIJl3plBzuazhIRYMmP_aUZCAQ', 6.76837, 43.42519, 43.5100099, 43.4064869, 6.933462, 6.7633719, '2015-11-22 19:47:14', '2015-11-22 19:47:14'),
            array('15d0c03f-4f0b-11e6-84b7-c8cbb8b4347f', '94000', 'ChIJl3xXlLIM5kcR8ITY4caCCxw', 2.4567614, 48.7884823, 48.8073389, 48.7619287, 2.4773736, 2.4274896, '2016-01-18 16:56:00', '2016-01-18 16:56:00'),
            array('15e74b81-4f0b-11e6-84b7-c8cbb8b4347f', 'Lyon', 'ChIJl4foalHq9EcR8CG75CqrCAQ', 4.835659, 45.764043, 45.8084251, 45.707486, 4.898393, 4.771849, '2015-11-22 17:42:19', '2015-11-22 17:42:19'),
            array('15fac901-4f0b-11e6-84b7-c8cbb8b4347f', 'Clamecy', 'ChIJl675Fdko7kcRumAy4rF5msY', 3.518769, 47.459605, 47.493885, 47.4239769, 3.576143, 3.461432, '2015-11-22 18:15:07', '2015-11-22 18:15:07'),
            array('160f1f2f-4f0b-11e6-84b7-c8cbb8b4347f', 'Guillestre', 'ChIJlbqA36CwzBIRhxusOGxTN3M', 6.649365, 44.6610919, 44.71703, 44.6022509, 6.805795, 6.5907779, '2015-11-22 19:17:53', '2015-11-22 19:17:53'),
            array('1623afe7-4f0b-11e6-84b7-c8cbb8b4347f', 'Peillonnex', 'ChIJLckiu-wMjEcRD4xBRujGTuU', 6.374968, 46.132179, 46.148165, 46.1170329, 6.4038661, 6.361043, '2016-03-15 19:01:55', '2016-03-15 19:01:55'),
            array('1637a021-4f0b-11e6-84b7-c8cbb8b4347f', 'Brunoy', 'ChIJldrA7nMK5kcR5zpOLP1udFM', 2.504543, 48.698071, 48.7130679, 48.676603, 2.529569, 2.4822361, '2015-11-22 20:41:30', '2015-11-22 20:41:30'),
            array('164c2aad-4f0b-11e6-84b7-c8cbb8b4347f', 'Chevilly Larue', 'ChIJLQuvkAF05kcRvTp5sUeXMok', 2.3581919, 48.7649765, 48.7794179, 48.756203, 2.368622, 2.3326811, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('165f50a8-4f0b-11e6-84b7-c8cbb8b4347f', 'Mandelieu-La Napoule', 'ChIJlQVjyyCDzhIRs3Su3K08rZ4', 6.938309, 43.546232, 43.5717799, 43.4949749, 6.957453, 6.878513, '2015-11-22 18:34:38', '2015-11-22 18:34:38'),
            array('16745583-4f0b-11e6-84b7-c8cbb8b4347f', 'Déville-lès-Rouen', 'ChIJLSxTdWbn4EcRIIe2T0gUDAQ', 1.049907, 49.469956, 49.480651, 49.4539109, 1.062925, 1.0361271, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('16890cab-4f0b-11e6-84b7-c8cbb8b4347f', 'Mouxy', 'ChIJlWqkICahi0cRKYQ-1B6VloU', 5.934296, 45.682001, 45.697096, 45.66404, 5.977082, 5.9189619, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('169e3975-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Grégoire', 'ChIJLWSq0ePdDkgRZEimrG0Fx7I', -1.683146, 48.153561, 48.1868479, 48.132689, -1.639173, -1.7238729, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('16b23397-4f0b-11e6-84b7-c8cbb8b4347f', 'Voiron', 'ChIJlwsXphrkikcR0GG-5CqrCAQ', 5.591349, 45.362713, 45.416142, 45.344806, 5.616992, 5.5569041, '2016-02-23 12:33:36', '2016-02-23 12:33:36'),
            array('16c84f7f-4f0b-11e6-84b7-c8cbb8b4347f', 'Tresserve', 'ChIJlxLA16qgi0cRcKi65CqrCAQ', 5.900216, 45.6764, 45.6945559, 45.658454, 5.9061801, 5.8926982, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('16dd7dfa-4f0b-11e6-84b7-c8cbb8b4347f', 'Verrières-le-Buisson', 'ChIJlYk9XsR55kcR8D-LaMOCCwQ', 2.266838, 48.746318, 48.766484, 48.735764, 2.285341, 2.2235821, '2015-11-22 20:54:31', '2015-11-22 20:54:31'),
            array('16f135b4-4f0b-11e6-84b7-c8cbb8b4347f', 'Chantepie', 'ChIJlyOtErPYDkgRYLbkNs2lDAQ', -1.618455, 48.089126, 48.099915, 48.0662199, -1.5631319, -1.649659, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('1705cb80-4f0b-11e6-84b7-c8cbb8b4347f', 'Tournefeuille', 'ChIJlYs7cYywrhIRUdK3qI0Lips', 1.346834, 43.5829, 43.5949769, 43.554388, 1.3770179, 1.2904889, '2015-11-22 19:41:27', '2015-11-22 19:41:27'),
            array('172038c8-4f0b-11e6-84b7-c8cbb8b4347f', 'Messery', 'ChIJlZITfAZCjEcRQJy65CqrCAQ', 6.292793, 46.351234, 46.364287, 46.330532, 6.327114, 6.269348, '2015-11-22 17:48:10', '2015-11-22 17:48:10'),
            array('173a33d9-4f0b-11e6-84b7-c8cbb8b4347f', 'Roanne', 'ChIJM_8Z6UAP9EcRQA3oy688CQQ', 4.072695, 46.034432, 46.071365, 46.0172211, 4.1124074, 4.047328, '2016-02-10 15:57:15', '2016-02-10 15:57:15'),
            array('17516b32-4f0b-11e6-84b7-c8cbb8b4347f', 'Crolles', 'ChIJM_pJdepZikcROctP7L16yj4', 5.882983, 45.284906, 45.30567, 45.25746, 5.919343, 5.850683, '2015-11-22 18:14:21', '2015-11-22 18:14:21'),
            array('1765ac09-4f0b-11e6-84b7-c8cbb8b4347f', 'Arcueil', 'ChIJm-9wxBFx5kcR8DmLaMOCCwQ', 2.334955, 48.80486, 48.81378, 48.7969019, 2.347739, 2.317842, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('177a1ac8-4f0b-11e6-84b7-c8cbb8b4347f', 'Villeneuve-d\'Ascq', 'ChIJM-LSQcrXwkcRWYfWVM7rqO0', 3.1442651, 50.6232523, 50.673368, 50.5990711, 3.196661, 3.1158851, '2015-11-22 18:13:08', '2015-11-22 18:13:08'),
            array('178e4819-4f0b-11e6-84b7-c8cbb8b4347f', 'Montcresson', 'ChIJM1-wp3OG70cRHUAQk__hdpI', 2.806047, 47.906915, 47.942275, 47.879916, 2.844901, 2.771446, '2015-11-22 20:30:39', '2015-11-22 20:30:39'),
            array('17a1ec01-4f0b-11e6-84b7-c8cbb8b4347f', 'Marseille', 'ChIJM1PaREO_yRIRIAKX_aUZCAQ', 5.36978, 43.296482, 43.39116, 43.169621, 5.5323519, 5.228641, '2015-11-22 17:42:19', '2015-11-22 17:42:19'),
            array('17b67474-4f0b-11e6-84b7-c8cbb8b4347f', 'Vence', 'ChIJM1TzaUnNzRIRIJaX_aUZCAQ', 7.1117033, 43.7223216, 43.7830489, 43.69162, 7.147037, 7.051874, '2015-11-22 19:10:32', '2015-11-22 19:10:32'),
            array('17cfc5e8-4f0b-11e6-84b7-c8cbb8b4347f', 'Lampaul-Ploudalmézeau', 'ChIJm278szefFkgR8GrlNs2lDAQ', -4.65311, 48.565121, 48.5839659, 48.533723, -4.6262929, -4.672141, '2015-11-22 19:14:11', '2015-11-22 19:14:11'),
            array('17e72554-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Sorlin-d\'Arves', 'ChIJm3bm-lw5ikcReX4iARIZhK0', 6.234955, 45.221576, 45.2478088, 45.1508829, 6.2572201, 6.1437971, '2015-11-22 20:58:28', '2015-11-22 20:58:28'),
            array('17fc8e37-4f0b-11e6-84b7-c8cbb8b4347f', 'Pézenas', 'ChIJM3hyuyhAsRIRu8ka1XHy8Ho', 3.423193, 43.461531, 43.5027339, 43.420506, 3.4630799, 3.3728379, '2016-04-25 14:59:35', '2016-04-25 14:59:35'),
            array('18124f9c-4f0b-11e6-84b7-c8cbb8b4347f', 'La Garenne-Colombes', 'ChIJm5HlRq5l5kcR4D2LaMOCCwQ', 2.244085, 48.906535, 48.913571, 48.9006479, 2.2581981, 2.229184, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('18283ea2-4f0b-11e6-84b7-c8cbb8b4347f', 'Pontcharra', 'ChIJm7sY81ZNikcRhohGmiMRPGs', 6.020171, 45.432395, 45.454046, 45.3896559, 6.050669, 5.9989421, '2015-11-22 18:06:28', '2015-11-22 18:06:28'),
            array('183ef3cc-4f0b-11e6-84b7-c8cbb8b4347f', 'Caen', 'ChIJM8kETL1CCkgRWQjQMJ90pT0', -0.370679, 49.182863, 49.2162688, 49.1529869, -0.330739, -0.4137789, '2015-11-22 20:33:05', '2015-11-22 20:33:05'),
            array('185378f3-4f0b-11e6-84b7-c8cbb8b4347f', 'Arette', 'ChIJm9hRioEEVw0RkrEg_-oxN1U', -0.71622, 43.095245, 43.1120468, 42.9488889, -0.675845, -0.7982311, '2015-11-22 18:19:26', '2015-11-22 18:19:26'),
            array('18690328-4f0b-11e6-84b7-c8cbb8b4347f', 'Autrans', 'ChIJMa6fiIjsikcRoIG-5CqrCAQ', 5.542716, 45.176098, 45.243939, 45.1551599, 5.597985, 5.507998, '2015-11-22 19:16:56', '2015-11-22 19:16:56'),
            array('187f4671-4f0b-11e6-84b7-c8cbb8b4347f', 'Lac de la Cavayère', 'ChIJma7QGwIrrhIRhb-bkAMzp8I', 2.4190185, 43.1849338, 43.1870741, 43.178289, 2.423978, 2.415953, '2016-01-15 15:13:16', '2016-01-15 15:13:16'),
            array('1892d59a-4f0b-11e6-84b7-c8cbb8b4347f', 'Trets', 'ChIJMa9YG06cyRIR1hirAceWy5k', 5.6841549, 43.446909, 43.4912379, 43.3991168, 5.7883849, 5.6390709, '2015-11-22 18:23:42', '2015-11-22 18:23:42'),
            array('18a7d0d2-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Mans', 'ChIJMarzFNKI4kcRf-B9akxdAmk', 0.199556, 48.00611, 48.0358609, 47.927934, 0.255099, 0.136286, '2016-01-19 18:06:24', '2016-01-19 18:06:24'),
            array('18be202a-4f0b-11e6-84b7-c8cbb8b4347f', 'Gennevilliers', 'ChIJMbQYxkVv5kcRdfF2DZJODJU', 2.293275, 48.925525, 48.950193, 48.9127339, 2.3281585, 2.2477849, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('18d3657c-4f0b-11e6-84b7-c8cbb8b4347f', 'Soisy-sous-Montmorency', 'ChIJMdCX8m9o5kcRAC2LaMOCCwQ', 2.299731, 48.988506, 49.002495, 48.9749989, 2.316584, 2.283996, '2016-01-06 14:47:45', '2016-01-06 14:47:45'),
            array('18e87a0b-4f0b-11e6-84b7-c8cbb8b4347f', 'Nice', 'ChIJMS2FahDQzRIRcJqX_aUZCAQ', 7.2619532, 43.7101728, 43.7609049, 43.6454759, 7.323481, 7.182153, '2015-11-22 17:41:00', '2015-11-22 17:41:00'),
            array('1909a203-4f0b-11e6-84b7-c8cbb8b4347f', 'Hauteluce', 'ChIJmUbIIk7gi0cRnvpnjowBnP0', 6.585506, 45.75054, 45.8006231, 45.731354, 6.6905961, 6.518251, '2015-11-22 17:45:26', '2015-11-22 17:45:26'),
            array('191f041d-4f0b-11e6-84b7-c8cbb8b4347f', 'France', 'ChIJMVd4MymgVA0R99lHx5Y__Ws', 2.213749, 46.227638, 51.0891283, 41.3423275, 9.5600677, -5.1423075, '2016-01-14 21:21:18', '2016-01-14 21:21:18'),
            array('1933368e-4f0b-11e6-84b7-c8cbb8b4347f', '91000', 'ChIJmwx6bKHg5UcRcHfY4caCCxw', 2.4429211, 48.6280893, 48.648511, 48.6108935, 2.4705591, 2.4131996, '2016-01-27 18:08:47', '2016-01-27 18:08:47'),
            array('194850d0-4f0b-11e6-84b7-c8cbb8b4347f', 'Monaco', 'ChIJMYU_e2_CzRIR_JzEOkx493Q', 7.4246158, 43.7384176, 43.7519029, 43.7247427, 7.4398113, 7.4091049, '2016-02-10 11:45:57', '2016-02-10 11:45:57'),
            array('195ccd91-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Pavillons-sous-Bois', 'ChIJN0kOwgkT5kcRADuLaMOCCwQ', 2.504403, 48.90852, 48.918751, 48.8952139, 2.521307, 2.4901399, '2016-03-17 20:35:34', '2016-03-17 20:35:34'),
            array('19740b3f-4f0b-11e6-84b7-c8cbb8b4347f', 'Font-Romeu-Odeillo-Via', 'ChIJn3t0vjdipRIRNd13go432Y4', 2.0322172, 42.5182933, 42.545498, 42.4752389, 2.0712969, 1.969436, '2015-11-22 17:40:06', '2015-11-22 17:40:06'),
            array('19884617-4f0b-11e6-84b7-c8cbb8b4347f', 'La Mézière', 'ChIJn6_D7CXmDkgRXHXAPBukcn8', -1.754105, 48.219363, 48.241282, 48.185643, -1.718948, -1.7819789, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('199d3d38-4f0b-11e6-84b7-c8cbb8b4347f', 'Argentière', 'ChIJn9yry7xMiUcRKy3DvNdKSSc', 6.926873, 45.983986, null, null, null, null, '2015-12-27 12:43:38', '2015-12-27 12:43:38'),
            array('19b2740c-4f0b-11e6-84b7-c8cbb8b4347f', 'Sèvres', 'ChIJNeiYQw575kcRbIc1tR8E4ww', 2.2109771, 48.8212407, 48.835555, 48.809257, 2.2323348, 2.178506, '2015-11-22 20:48:44', '2015-11-22 20:48:44'),
            array('19c5ac16-4f0b-11e6-84b7-c8cbb8b4347f', 'Cassis', 'ChIJNeR-a8a6yRIRMASX_aUZCAQ', 5.53712, 43.215134, 43.2485779, 43.188456, 5.594293, 5.506295, '2016-01-11 15:01:56', '2016-01-11 15:01:56'),
            array('19daf620-4f0b-11e6-84b7-c8cbb8b4347f', 'Isneauville', 'ChIJNQTSm2Hb4EcRr7NXQ8UnW5U', 1.142236, 49.49836, 49.520527, 49.485966, 1.1741171, 1.120477, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('19ef1596-4f0b-11e6-84b7-c8cbb8b4347f', 'Laissaud', 'ChIJNVJDF6Syi0cRoLG65CqrCAQ', 6.0393896, 45.4479376, 45.471753, 45.4378979, 6.0552979, 6.008488, '2015-11-22 18:06:28', '2015-11-22 18:06:28'),
            array('1a0344fc-4f0b-11e6-84b7-c8cbb8b4347f', 'Auriol', 'ChIJnwChvsiYyRIRhBJ00_v5MCo', 5.631363, 43.369174, 43.401959, 43.317008, 5.7051149, 5.6068809, '2016-01-11 14:47:47', '2016-01-11 14:47:47'),
            array('1a16b211-4f0b-11e6-84b7-c8cbb8b4347f', 'Villard-Reculas', 'ChIJny40pGRpikcR3fu1VsIuF_w', 6.031327, 45.0914689, 45.105603, 45.0791699, 6.0561411, 6.021981, '2015-11-22 16:04:15', '2015-11-22 16:04:15'),
            array('1a2ab766-4f0b-11e6-84b7-c8cbb8b4347f', 'Angers', 'ChIJnY7lANp4CEgRMJwNHlI3DQQ', -0.563166, 47.478419, 47.5263839, 47.4373929, -0.508143, -0.617726, '2016-01-20 16:40:43', '2016-01-20 16:40:43'),
            array('1a3e9d65-4f0b-11e6-84b7-c8cbb8b4347f', 'Cesson-Sévigné', 'ChIJNYvWwzHZDkgRQJAskxjmslU', -1.602923, 48.119472, 48.160392, 48.0865819, -1.5503189, -1.636846, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('1a5408bc-4f0b-11e6-84b7-c8cbb8b4347f', 'Carrières-sur-Seine', 'ChIJnz5TN45j5kcR4EKMaMOCCwQ', 2.178685, 48.911292, 48.926509, 48.8992728, 2.203844, 2.158603, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('1a67fee9-4f0b-11e6-84b7-c8cbb8b4347f', 'Épinal', 'ChIJnzmpjH2gk0cR3kbdUHWkKO0', 6.449403, 48.172402, 48.207972, 48.119363, 6.579655, 6.3937099, '2015-11-22 20:39:11', '2015-11-22 20:39:11'),
            array('1a7c610b-4f0b-11e6-84b7-c8cbb8b4347f', 'Labastidette', 'ChIJO-mh94vKrhIRQNd5-_XDgAM', 1.245237, 43.460357, 43.4691089, 43.441742, 1.2616939, 1.220502, '2015-11-22 19:02:54', '2015-11-22 19:02:54'),
            array('1a90fbc5-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Monêtier-les-Bains', 'ChIJo015pEP3iUcRmv6e_uLixMY', 6.507839, 44.976254, 45.070179, 44.9282969, 6.5800741, 6.3617581, '2015-11-22 16:14:32', '2015-11-22 16:14:32'),
            array('1aa61dc8-4f0b-11e6-84b7-c8cbb8b4347f', 'Sotteville-lès-Rouen', 'ChIJO4L4cVre4EcRkGq2T0gUDAQ', 1.095407, 49.41725, 49.4260339, 49.397178, 1.117808, 1.071067, '2016-01-29 17:04:48', '2016-01-29 17:04:48'),
            array('1aba2080-4f0b-11e6-84b7-c8cbb8b4347f', 'Gruissan', 'ChIJo6Z8omhVsBIRlnjTnoD_A0E', 3.086181, 43.107052, 43.1588488, 43.049456, 3.1495609, 3.022524, '2015-12-04 10:19:50', '2015-12-04 10:19:50'),
            array('1ace130f-4f0b-11e6-84b7-c8cbb8b4347f', 'Pessac', 'ChIJo7h8IvnYVA0RmF_5SzMdzjo', -0.630386, 44.80583, 44.8226669, 44.7494189, -0.6029481, -0.7589951, '2015-11-22 18:21:21', '2015-11-22 18:21:21'),
            array('1afab1b9-4f0b-11e6-84b7-c8cbb8b4347f', 'Pipay', 'ChIJoe_U8BBFikcRVF4BncuFa2U', 6.014998, 45.26505, null, null, null, null, '2015-11-22 16:33:47', '2015-11-22 16:33:47'),
            array('1b1108b7-4f0b-11e6-84b7-c8cbb8b4347f', 'Brignoles', 'ChIJOfbSfOVByRIRQM-P_aUZCAQ', 6.061187, 43.40655, 43.433059, 43.342773, 6.1754749, 5.9818049, '2016-01-22 14:55:36', '2016-01-22 14:55:36'),
            array('1b25d9a1-4f0b-11e6-84b7-c8cbb8b4347f', 'Salon-de-Provence', 'ChIJofoBqDMAthIRIP-W_aUZCAQ', 5.097022, 43.640199, 43.688584, 43.597994, 5.143973, 4.9540909, '2015-11-22 18:30:48', '2015-11-22 18:30:48'),
            array('1b39ce76-4f0b-11e6-84b7-c8cbb8b4347f', 'Crosne', 'ChIJofQsecQK5kcRgEeLaMOCCwQ', 2.461891, 48.720171, 48.729594, 48.7107749, 2.4763941, 2.447426, '2015-11-22 20:41:30', '2015-11-22 20:41:30'),
            array('1b4e01a8-4f0b-11e6-84b7-c8cbb8b4347f', 'Villard-de-Lans', 'ChIJOQbkieGQikcRwGK-5CqrCAQ', 5.55227, 45.070641, 45.102065, 45.0000029, 5.613579, 5.464847, '2015-11-22 16:17:59', '2015-11-22 16:17:59'),
            array('1b628701-4f0b-11e6-84b7-c8cbb8b4347f', 'Fontaine', 'ChIJOQuEHQfzikcRAHm-5CqrCAQ', 5.688211, 45.192754, 45.205916, 45.1820179, 5.7017771, 5.6508171, '2015-11-22 18:56:52', '2015-11-22 18:56:52'),
            array('1b796e4a-4f0b-11e6-84b7-c8cbb8b4347f', 'Vallorcine', 'ChIJoTFYf-SyjkcR8JW65CqrCAQ', 6.931817, 46.033505, 46.0665489, 45.9877369, 6.9628799, 6.844598, '2015-11-22 17:26:21', '2015-11-22 17:26:21'),
            array('1b8e0547-4f0b-11e6-84b7-c8cbb8b4347f', 'Nimes', 'ChIJOVPo1gsttBIRAwwgn08TiN4', 4.360054, 43.836699, 43.9228799, 43.741438, 4.4499169, 4.23579, '2015-11-22 17:39:09', '2015-11-22 17:39:09'),
            array('1ba30f5f-4f0b-11e6-84b7-c8cbb8b4347f', 'Chalon-sur-Saône', 'ChIJOWbs5Jf88kcRcPAOszTOCQQ', 4.853947, 46.780764, 46.819013, 46.760277, 4.8813981, 4.8194929, '2016-01-22 11:07:46', '2016-01-22 11:07:46'),
            array('1bb73d11-4f0b-11e6-84b7-c8cbb8b4347f', 'Bourg-Saint-Maurice', 'ChIJOXYlgjRmiUcRsLa65CqrCAQ', 6.769548, 45.618598, 45.779755, 45.5476709, 6.852085, 6.6708751, '2015-11-22 19:04:55', '2015-11-22 19:04:55'),
            array('1bcb8015-4f0b-11e6-84b7-c8cbb8b4347f', 'Eure-et-Loir', 'ChIJozitvHMK5EcRICczBdfIDQM', 1.1989814, 48.5525242, 48.941029, 47.9538179, 1.99456, 0.755676, '2016-01-18 12:39:54', '2016-01-18 12:39:54'),
            array('1be1594a-4f0b-11e6-84b7-c8cbb8b4347f', 'Sillingy', 'ChIJP_eu4oiDi0cR5_J-1jVtlcY', 6.035302, 45.946611, 45.974373, 45.9290499, 6.083196, 5.986793, '2015-11-22 17:46:29', '2015-11-22 17:46:29'),
            array('1bf76b04-4f0b-11e6-84b7-c8cbb8b4347f', 'La Giettaz', 'ChIJP-InVDfli0cR9MZLYViSbWA', 6.49526, 45.862088, 45.9091009, 45.8445109, 6.572883, 6.444711, '2016-03-15 18:42:44', '2016-03-15 18:42:44'),
            array('1c0c7c66-4f0b-11e6-84b7-c8cbb8b4347f', 'Lans-en-Vercors', 'ChIJP0mSqViSikcRAHe-5CqrCAQ', 5.5882699, 45.1279759, 45.1672219, 45.0835739, 5.6322721, 5.539332, '2015-11-22 19:16:56', '2015-11-22 19:16:56'),
            array('1c214b27-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Bettex', 'ChIJP1mxCJxXiUcRUsBz4yurCCY', 6.688169, 45.874248, null, null, null, null, '2015-11-27 14:21:26', '2015-11-27 14:21:26'),
            array('1c35fb23-4f0b-11e6-84b7-c8cbb8b4347f', 'Saas Fee', 'ChIJp2CD9qJFj0cRzho9eqWJgOU', 7.9297122, 46.1091073, 46.1254399, 46.0457699, 7.94686, 7.8570301, '2016-01-29 14:48:45', '2016-01-29 14:48:45'),
            array('1c4c1686-4f0b-11e6-84b7-c8cbb8b4347f', 'Val d\'Isère Ski Resort', 'ChIJp5ziwugLiUcRoEVHY9WU8KI', 6.980226, 45.448034, null, null, null, null, '2015-12-03 16:44:24', '2015-12-03 16:44:24'),
            array('1c60e977-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Chalets de Pré Genty', 'ChIJp6722nBqikcRS_LebyDye4c', 6.0741091, 45.1313905, null, null, null, null, '2015-11-22 16:13:27', '2015-11-22 16:13:27'),
            array('1c76e98e-4f0b-11e6-84b7-c8cbb8b4347f', 'La Mongie', 'ChIJp6dQyvo8qBIRYBUiup72Bgo', 0.182806, 42.910254, null, null, null, null, '2015-11-22 18:23:23', '2015-11-22 18:23:23'),
            array('1c8bb31f-4f0b-11e6-84b7-c8cbb8b4347f', '95000', 'ChIJP8mTDLX05kcRAI7Y4caCCxw', 2.0547222, 49.0331671, 49.049208, 49.0016929, 2.0913125, 1.9915009, '2016-01-20 16:02:09', '2016-01-20 16:02:09'),
            array('1ca19cb2-4f0b-11e6-84b7-c8cbb8b4347f', 'Sault', 'ChIJp8tPxZBCyhIRGRzdLRbp--c', 5.408174, 44.091413, 44.159596, 43.995267, 5.475291, 5.303177, '2015-11-22 18:20:13', '2015-11-22 18:20:13'),
            array('1cb67a75-4f0b-11e6-84b7-c8cbb8b4347f', 'Toucy', 'ChIJPc-Pwja270cRYFUNszTOCQQ', 3.29435, 47.7350629, 47.7697439, 47.695882, 3.342651, 3.2337721, '2015-11-22 18:15:07', '2015-11-22 18:15:07'),
            array('1ccb78c1-4f0b-11e6-84b7-c8cbb8b4347f', 'Melesse', 'ChIJPeAXBE_dDkgRIK_kNs2lDAQ', -1.697343, 48.216365, 48.2572809, 48.181056, -1.644549, -1.7328909, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('1cdfdce6-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Bourg-d\'Oisans', 'ChIJPRszQRtsikcRbtbm4mHBfig', 6.030064, 45.051951, 45.1213029, 44.9355849, 6.080122, 5.9756349, '2015-11-22 18:07:38', '2015-11-22 18:07:38'),
            array('1cf7ef01-4f0b-11e6-84b7-c8cbb8b4347f', 'Sallanches', 'ChIJpWDtzTr8i0cRAJi65CqrCAQ', 6.6305569, 45.936661, 45.987036, 45.8774869, 6.693316, 6.5149229, '2015-11-22 19:11:31', '2015-11-22 19:11:31'),
            array('1d0e7ea6-4f0b-11e6-84b7-c8cbb8b4347f', 'Barbâtre', 'ChIJpXtOZFYaBUgREL0JHlI3DQQ', -2.178589, 46.9425669, 46.9610929, 46.893477, -2.14679, -2.19774, '2015-12-03 12:12:34', '2015-12-03 12:12:34'),
            array('1d24e3d8-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Pleynet', 'ChIJPyShRupFikcRkOAOoN2uLuU', 6.055841, 45.272203, null, null, null, null, '2015-11-22 16:33:47', '2015-11-22 16:33:47'),
            array('1d38e00e-4f0b-11e6-84b7-c8cbb8b4347f', 'Rouen', 'ChIJq_pxynbe4EcR1pOfpO-rHD0', 1.099971, 49.443232, 49.4652409, 49.417292, 1.1521091, 1.0310008, '2016-01-20 12:11:24', '2016-01-20 12:11:24'),
            array('1d542a45-4f0b-11e6-84b7-c8cbb8b4347f', 'Stade de Neige du Margériaz', 'ChIJq0dg9Muki0cR4naqPuamKkA', 6.0613471, 45.6427467, null, null, null, null, '2016-04-28 23:55:04', '2016-04-28 23:55:04'),
            array('1d6fd4c3-4f0b-11e6-84b7-c8cbb8b4347f', 'Lamure-sur-Azergues', 'ChIJq1hadTZ59EcR2H3q-1pfTpM', 4.491787, 46.063396, 46.0758159, 46.030184, 4.540729, 4.4770501, '2015-11-22 19:42:43', '2015-11-22 19:42:43'),
            array('1d861391-4f0b-11e6-84b7-c8cbb8b4347f', 'La Grave', 'ChIJQ1v8quIZikcRQKeX_aUZCAQ', 6.304706, 45.045842, 45.126851, 44.9951429, 6.3748461, 6.2023631, '2015-11-22 18:11:21', '2015-11-22 18:11:21'),
            array('1d9d1528-4f0b-11e6-84b7-c8cbb8b4347f', 'Iceland', 'ChIJQ2Dro1Ir0kgRmkXB5TQEim8', -19.020835, 64.963051, 66.5663183, 63.2962342, -13.4961428, -24.5333969, '2015-12-02 15:31:44', '2015-12-02 15:31:44'),
            array('1db34d76-4f0b-11e6-84b7-c8cbb8b4347f', 'Prapoutel', 'ChIJQ3m3PShFikcRMBTzD5_Aiwk', 5.99299, 45.255966, null, null, null, null, '2015-11-22 16:29:11', '2015-11-22 16:29:11'),
            array('1dc7bd03-4f0b-11e6-84b7-c8cbb8b4347f', 'Abondance', 'ChIJq41BHTwejEcRoKa65CqrCAQ', 6.720362, 46.280704, 46.3093159, 46.1959662, 6.8070011, 6.6597381, '2016-02-04 15:20:58', '2016-02-04 15:20:58'),
            array('1ddc9d0a-4f0b-11e6-84b7-c8cbb8b4347f', 'Alfortville', 'ChIJq4LGBhNz5kcRADqLaMOCCwQ', 2.422317, 48.80093, 48.8167979, 48.776092, 2.433904, 2.4096893, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('1df21845-4f0b-11e6-84b7-c8cbb8b4347f', 'Vallouise', 'ChIJQ5tyxHz-iUcRTp9P49ZzTSY', 6.488418, 44.846704, 44.8729629, 44.7729479, 6.5350561, 6.3295981, '2015-11-22 19:43:23', '2015-11-22 19:43:23'),
            array('1e076841-4f0b-11e6-84b7-c8cbb8b4347f', 'Poitiers', 'ChIJq7OeQ3K-_UcRk5DgxRkj3pc', 0.340375, 46.580224, 46.627038, 46.5422299, 0.45185, 0.291089, '2016-01-22 15:50:20', '2016-01-22 15:50:20'),
            array('1e1d1bea-4f0b-11e6-84b7-c8cbb8b4347f', 'Lullin', 'ChIJQbYpyeYWjEcRwJ265CqrCAQ', 6.521883, 46.28433, 46.2997039, 46.253358, 6.539615, 6.481788, '2015-11-30 09:59:10', '2015-11-30 09:59:10'),
            array('1e323408-4f0b-11e6-84b7-c8cbb8b4347f', 'Metz', 'ChIJqbZ0YBvclEcRaaa7Nbbka1k', 6.1757156, 49.1193089, 49.148856, 49.0608499, 6.25681, 6.1360021, '2015-11-22 19:00:10', '2015-11-22 19:00:10'),
            array('1e4658e2-4f0b-11e6-84b7-c8cbb8b4347f', '69003', 'ChIJqfoTu3fq9EcRMHzkQS6rCBw', 4.855387, 45.75826, 45.7637524, 45.7390121, 4.8983666, 4.8384559, '2016-03-09 12:39:21', '2016-03-09 12:39:21'),
            array('1e5ab62b-4f0b-11e6-84b7-c8cbb8b4347f', 'Valmeinier', 'ChIJqQ5DjPWKiUcRAKi65CqrCAQ', 6.478624, 45.183414, 45.203884, 45.098574, 6.5639071, 6.453995, '2015-12-24 15:42:58', '2015-12-24 15:42:58'),
            array('1e6fecc4-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Perreux-sur-Marne', 'ChIJqRUXMIMN5kcRQDiLaMOCCwQ', 2.504038, 48.841386, 48.8572999, 48.829334, 2.51767, 2.4913941, '2015-11-22 18:57:43', '2015-11-22 18:57:43'),
            array('1e84f89d-4f0b-11e6-84b7-c8cbb8b4347f', 'Gex', 'ChIJqVG9_AVgjEcR0A7C5CqrCAQ', 6.058036, 46.3341909, 46.387109, 46.315807, 6.0978591, 5.9998401, '2016-01-19 16:35:42', '2016-01-19 16:35:42'),
            array('1e9a1987-4f0b-11e6-84b7-c8cbb8b4347f', 'Bègles', 'ChIJQVoEEvImVQ0RgO4WSBdlBgQ', -0.543784, 44.812023, 44.822821, 44.7831818, -0.5230481, -0.573954, '2015-11-22 20:36:34', '2015-11-22 20:36:34'),
            array('1eb0ee3e-4f0b-11e6-84b7-c8cbb8b4347f', 'Wattignies', 'ChIJQW1LU1fUwkcRBNT55iheWc0', 3.044099, 50.581494, 50.6012389, 50.5710769, 3.0655711, 3.012266, '2015-11-22 18:13:08', '2015-11-22 18:13:08'),
            array('1ecd70d4-4f0b-11e6-84b7-c8cbb8b4347f', '9', 'ChIJqxD2gD1w5kcRlqDNOpofZ2g', 2.2986733, 48.8444011, null, null, null, null, '2015-12-09 12:04:51', '2015-12-09 12:04:51'),
            array('1ee3ae64-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Maur-des-Fossés', 'ChIJqYYto_AM5kcRPU6QvoQL3bA', 2.485429, 48.8029439, 48.815758, 48.7840399, 2.5235241, 2.4606071, '2015-11-22 19:44:06', '2015-11-22 19:44:06'),
            array('1ef91b06-4f0b-11e6-84b7-c8cbb8b4347f', 'Antibes', 'ChIJqZFankXVzRIRsJ-X_aUZCAQ', 7.125102, 43.580418, 43.6226999, 43.541863, 7.1451269, 7.064568, '2016-01-22 15:55:48', '2016-01-22 15:55:48'),
            array('1f0e778c-4f0b-11e6-84b7-c8cbb8b4347f', 'Lantosque', 'ChIJr_B7GQG7zRIRUJuX_aUZCAQ', 7.311804, 43.974448, 44.0177679, 43.939632, 7.380172, 7.243995, '2015-11-22 19:10:32', '2015-11-22 19:10:32'),
            array('1f23975f-4f0b-11e6-84b7-c8cbb8b4347f', 'Douvaine', 'ChIJR_N1TypqjEcRgKC65CqrCAQ', 6.299197, 46.3048689, 46.3258149, 46.289249, 6.328433, 6.2731051, '2015-11-22 17:48:10', '2015-11-22 17:48:10'),
            array('1f384f0d-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Tropez', 'ChIJR-cWgxTHzhIR37mELWMbKpE', 6.6407109, 43.2676808, 43.279045, 43.2446889, 6.703852, 6.6237149, '2016-01-19 14:01:24', '2016-01-19 14:01:24'),
            array('1f4e7d20-4f0b-11e6-84b7-c8cbb8b4347f', 'Monthey', 'ChIJR-W61sG8jkcRZzRRKKVs2uY', 6.9469598, 46.2521873, 46.2799399, 46.20504, 6.97938, 6.90399, '2015-11-22 17:46:18', '2015-11-22 17:46:18'),
            array('1f631ab7-4f0b-11e6-84b7-c8cbb8b4347f', 'Liffré', 'ChIJR15xYBbQDkgRte-h77XD6m4', -1.505816, 48.214048, 48.2421191, 48.1627939, -1.3983919, -1.61442, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('1f7904bc-4f0b-11e6-84b7-c8cbb8b4347f', 'Veigy-Foncenex', 'ChIJR3RMpjNpjEcRxKFgwM0LPx4', 6.2736508, 46.2722263, 46.292262, 46.24768, 6.3008661, 6.23758, '2015-11-22 17:48:10', '2015-11-22 17:48:10'),
            array('1f8d704e-4f0b-11e6-84b7-c8cbb8b4347f', 'Quimper', 'ChIJr64TLpfVEEgR0M2iyu-FLdM', -4.097899, 47.997542, 48.066038, 47.930217, -4.0123019, -4.1821189, '2016-01-22 14:44:30', '2016-01-22 14:44:30'),
            array('1fa18cdd-4f0b-11e6-84b7-c8cbb8b4347f', 'Eaubonne', 'ChIJr6WmH9tn5kcRoDOLaMOCCwQ', 2.2779189, 48.992128, 49.00669, 48.9757829, 2.2928271, 2.263343, '2016-01-06 14:47:45', '2016-01-06 14:47:45'),
            array('1fb69d94-4f0b-11e6-84b7-c8cbb8b4347f', 'Colomiers', 'ChIJR7x63TmwrhIRka5gVptSEIA', 1.336579, 43.612189, 43.634005, 43.5892999, 1.366747, 1.2822619, '2015-11-22 19:41:27', '2015-11-22 19:41:27'),
            array('1fcc335e-4f0b-11e6-84b7-c8cbb8b4347f', 'Doussard', 'ChIJR9k071WUi0cROaAzIRY4Mi8', 6.220515, 45.776496, 45.8197142, 45.703893, 6.251696, 6.183457, '2016-01-26 20:26:51', '2016-01-26 20:26:51'),
            array('1fe0b6dc-4f0b-11e6-84b7-c8cbb8b4347f', 'Nantes', 'ChIJra6o8IHuBUgRMO0NHlI3DQQ', -1.553621, 47.218371, 47.2958269, 47.180617, -1.4802542, -1.6416766, '2016-01-20 00:07:33', '2016-01-20 00:07:33'),
            array('1ff60720-4f0b-11e6-84b7-c8cbb8b4347f', 'Fresnicourt-le-Dolmen', 'ChIJraWGhos93UcREO9jgT7xCgQ', 2.599756, 50.418059, 50.4442299, 50.4067469, 2.6306071, 2.575241, '2015-11-22 18:29:21', '2015-11-22 18:29:21'),
            array('200f5083-4f0b-11e6-84b7-c8cbb8b4347f', 'Cernay', 'ChIJrcWp8w-BkUcR615KGS_Cdzc', 7.176532, 47.807124, 47.819119, 47.7673829, 7.2123591, 7.1389071, '2015-11-22 19:07:39', '2015-11-22 19:07:39'),
            array('20249d78-4f0b-11e6-84b7-c8cbb8b4347f', 'Millau', 'ChIJrfdeFhhLshIRPvw0pWSCyRc', 3.077801, 44.100575, 44.179997, 44.016511, 3.2446039, 2.990659, '2015-11-22 19:45:53', '2015-11-22 19:45:53'),
            array('20418e46-4f0b-11e6-84b7-c8cbb8b4347f', 'Montargis', 'ChIJRfYnrC5-5UcRGfRgygf07WI', 2.736291, 47.99729, 48.012291, 47.9872569, 2.759669, 2.7180631, '2015-11-22 20:30:39', '2015-11-22 20:30:39'),
            array('20572213-4f0b-11e6-84b7-c8cbb8b4347f', 'Gentilly', 'ChIJRQ0vknRx5kcRIDmLaMOCCwQ', 2.344688, 48.8138999, 48.8184889, 48.80537, 2.3563311, 2.329045, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('206ca0a6-4f0b-11e6-84b7-c8cbb8b4347f', 'Marines', 'ChIJrQtYWlXw5kcRD6gX6Zs5vos', 1.982861, 49.1452219, 49.1692919, 49.1305509, 2.0049761, 1.959942, '2015-11-22 19:13:13', '2015-11-22 19:13:13'),
            array('20832868-4f0b-11e6-84b7-c8cbb8b4347f', 'Ivry-sur-Seine', 'ChIJrTUyArVz5kcRT3C_fI0RyF0', 2.38822, 48.813055, 48.8253937, 48.7992999, 2.4094442, 2.364116, '2015-11-22 20:32:19', '2015-11-22 20:32:19'),
            array('2099cea9-4f0b-11e6-84b7-c8cbb8b4347f', 'Clermont-l\'Hérault', 'ChIJrV7jI5tdsRIR1XuYn_l60cs', 3.429495, 43.62759, 43.668159, 43.596476, 3.474488, 3.3295819, '2015-11-22 17:59:48', '2015-11-22 17:59:48'),
            array('20b071de-4f0b-11e6-84b7-c8cbb8b4347f', 'Menthon-Saint-Bernard', 'ChIJrwSJiZKRi0cRgJy65CqrCAQ', 6.194737, 45.860543, 45.883279, 45.852897, 6.218201, 6.184796, '2015-11-22 17:50:12', '2015-11-22 17:50:12'),
            array('20c58295-4f0b-11e6-84b7-c8cbb8b4347f', 'Drumettaz-Clarafond', 'ChIJRxHjntimi0cR0LO65CqrCAQ', 5.9210299, 45.6603472, 45.67736, 45.644582, 5.9686151, 5.9068441, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('20da6097-4f0b-11e6-84b7-c8cbb8b4347f', 'Villeurbanne', 'ChIJryN2zS_A9EcRUiZbJSsMusM', 4.8901709, 45.771944, 45.795069, 45.748447, 4.921229, 4.858424, '2015-11-22 18:14:07', '2015-11-22 18:14:07'),
            array('20f109e6-4f0b-11e6-84b7-c8cbb8b4347f', 'La Grande-Motte', 'ChIJRzvEGAeYthIRoLlqFiGIBwQ', 4.086072, 43.560704, 43.5900919, 43.5519299, 4.109324, 4.0347648, '2015-11-23 21:20:36', '2015-11-23 21:20:36'),
            array('21258d3b-4f0b-11e6-84b7-c8cbb8b4347f', 'Rhone-Alpes', 'ChIJS_HI7dPZikcRua8Cids1k3c', 5.4502821, 45.1695797, 46.519953, 44.115493, 7.1855661, 3.6889129, '2016-03-09 12:39:21', '2016-03-09 12:39:21'),
            array('213e586a-4f0b-11e6-84b7-c8cbb8b4347f', 'Champéry', 'ChIJS_tdXA2ljkcRYVOEejgedpM', 6.8747591, 46.1816543, 46.19004, 46.1293879, 6.9101701, 6.790269, '2016-02-04 15:20:58', '2016-02-04 15:20:58'),
            array('2154fcf1-4f0b-11e6-84b7-c8cbb8b4347f', 'Colombes', 'ChIJS0wd8shl5kcRID6LaMOCCwQ', 2.2533313, 48.9220615, 48.937545, 48.9059169, 2.273291, 2.2211611, '2015-11-22 18:08:55', '2015-11-22 18:08:55'),
            array('216adae7-4f0b-11e6-84b7-c8cbb8b4347f', 'Pyrénées-Orientales', 'ChIJs3vVe5L9rxIRoCllFiGIBwM', 2.539603, 42.6012912, 42.9185399, 42.333014, 3.177833, 1.721635, '2016-01-15 15:13:16', '2016-01-15 15:13:16'),
            array('2180d93c-4f0b-11e6-84b7-c8cbb8b4347f', 'Muret', 'ChIJsaEjLYPJrhIRMBpBL5z2BgQ', 1.33071, 43.461574, 43.4922169, 43.389225, 1.3747759, 1.2226649, '2015-11-22 19:02:54', '2015-11-22 19:02:54'),
            array('219892d1-4f0b-11e6-84b7-c8cbb8b4347f', 'Troyes', 'ChIJsbeH51eY7kcRdU_oZlUS3Vc', 4.0744009, 48.2973451, 48.3185979, 48.2662149, 4.1111471, 4.041094, '2016-01-29 14:13:04', '2016-01-29 14:13:04'),
            array('21aefc18-4f0b-11e6-84b7-c8cbb8b4347f', 'Eygliers', 'ChIJSbn5SOuwzBIRc68FriJ1zN0', 6.6343, 44.676128, 44.7292209, 44.663199, 6.719902, 6.6043339, '2015-11-22 19:17:53', '2015-11-22 19:17:53'),
            array('21c4fbda-4f0b-11e6-84b7-c8cbb8b4347f', 'Martigues', 'ChIJSdKFn9EfthIRmlyw6ck9db4', 5.053728, 43.404811, 43.443478, 43.3243169, 5.1050508, 4.9850941, '2015-11-22 19:46:36', '2015-11-22 19:46:36'),
            array('21dae753-4f0b-11e6-84b7-c8cbb8b4347f', 'Crest', 'ChIJsf6EgS1YtRIR0MS_5CqrCAQ', 5.024072, 44.7282749, 44.759852, 44.7024159, 5.062269, 4.980014, '2015-11-22 18:22:47', '2015-11-22 18:22:47'),
            array('21f0d07c-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Bon-Tarentaise', 'ChIJSQrYmj-CiUcRwKy65CqrCAQ', 6.637265, 45.433942, 45.4458839, 45.3278879, 6.690066, 6.601168, '2015-11-22 19:18:57', '2015-11-22 19:18:57'),
            array('2207068e-4f0b-11e6-84b7-c8cbb8b4347f', 'Mâcot-la-Plagne', 'ChIJsUj_XAF7iUcRILG65CqrCAQ', 6.674054, 45.553292, 45.564309, 45.480578, 6.734643, 6.657026, '2015-11-22 16:17:01', '2015-11-22 16:17:01'),
            array('221d4511-4f0b-11e6-84b7-c8cbb8b4347f', 'Salazie', 'ChIJsVfD2tR-eCERFZFj7lLZes8', 55.5136433, -21.0447249, -20.9932159, -21.1116761, 55.5853029, 55.4438192, '2015-12-01 18:00:26', '2015-12-01 18:00:26'),
            array('223483b7-4f0b-11e6-84b7-c8cbb8b4347f', 'Bois-Colombes', 'ChIJsVRShI1l5kcRByBPI_T_gJI', 2.269675, 48.917402, 48.926849, 48.9033819, 2.2806311, 2.2570251, '2015-11-22 18:11:55', '2015-11-22 18:11:55'),
            array('224a3ab0-4f0b-11e6-84b7-c8cbb8b4347f', 'Conquet', 'ChIJSwnFXvNstRIRM3iTBfoaCgc', 4.76377, 44.5066119, null, null, null, null, '2015-11-22 19:14:11', '2015-11-22 19:14:11'),
            array('2260146c-4f0b-11e6-84b7-c8cbb8b4347f', 'Courchevel 1650 (Moriond)', 'ChIJSx7CVr14iUcRZTWOX9TW9_c', 6.653138, 45.417003, null, null, null, null, '2016-03-01 23:31:57', '2016-03-01 23:31:57'),
            array('22766713-4f0b-11e6-84b7-c8cbb8b4347f', 'Vitry-sur-Seine', 'ChIJSYqKQJFz5kcR-uqmUec4XNw', 2.39851, 48.792001, 48.8084527, 48.7699989, 2.4219582, 2.367326, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('228b434a-4f0b-11e6-84b7-c8cbb8b4347f', 'Montpellier', 'ChIJsZ3dJQevthIRAuiUKHRWh60', 3.876716, 43.610769, 43.6532999, 43.566744, 3.941279, 3.807044, '2015-11-22 17:39:09', '2015-11-22 17:39:09'),
            array('22a086bc-4f0b-11e6-84b7-c8cbb8b4347f', 'Choisy-le-Roi', 'ChIJSzO-mZB05kcRYDmLaMOCCwQ', 2.4088759, 48.762541, 48.7771412, 48.749752, 2.442481, 2.398048, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('22b61c68-4f0b-11e6-84b7-c8cbb8b4347f', '14th arrondissement of Paris', 'ChIJT0ClqLNx5kcRIBuUaMOCCwU', 2.3255684, 48.8314408, 48.8436181, 48.815748, 2.3445619, 2.301202, '2015-11-22 18:58:41', '2015-11-22 18:58:41'),
            array('22cc5059-4f0b-11e6-84b7-c8cbb8b4347f', 'Corrençon-en-Vercors', 'ChIJt3kPXr6QikcRcJhmCYpiPSI', 5.526594, 45.031402, 45.055236, 44.967438, 5.56379, 5.4804041, '2015-11-22 18:32:40', '2015-11-22 18:32:40'),
            array('22e16467-4f0b-11e6-84b7-c8cbb8b4347f', 'Nogent-sur-Marne', 'ChIJt5RRyGsN5kcRkDiLaMOCCwQ', 2.481699, 48.837631, 48.847774, 48.8261349, 2.4976281, 2.4645891, '2015-11-22 18:56:12', '2015-11-22 18:56:12'),
            array('22f7324a-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Médard-en-Jalles', 'ChIJT84PpVLOVA0RgNUWSBdlBgQ', -0.7181, 44.895295, 44.925073, 44.8470869, -0.6804991, -0.8840781, '2015-11-22 18:12:26', '2015-11-22 18:12:26'),
            array('230c237a-4f0b-11e6-84b7-c8cbb8b4347f', 'Font Romeu', 'ChIJta2HIEZipRIRiBPXwTh6xhQ', 2.043402, 42.503412, null, null, null, null, '2015-11-22 18:18:24', '2015-11-22 18:18:24'),
            array('2321de98-4f0b-11e6-84b7-c8cbb8b4347f', 'Aulnay-sous-Bois', 'ChIJTa87fmMT5kcRUEoqfjjYsNo', 2.4970711, 48.9412151, 48.974582, 48.9169039, 2.5241141, 2.4598411, '2016-03-17 20:35:34', '2016-03-17 20:35:34'),
            array('2336f35e-4f0b-11e6-84b7-c8cbb8b4347f', '77000', 'ChIJtbda66bw5UcRoFrY4caCCxw', 2.684649, 48.5175192, 48.5606966, 48.4870948, 2.7340105, 2.6287111, '2016-01-18 17:42:45', '2016-01-18 17:42:45'),
            array('234d12d1-4f0b-11e6-84b7-c8cbb8b4347f', 'Ermont', 'ChIJtbh2dbpn5kcRIDOLaMOCCwQ', 2.258451, 48.989071, 49.001849, 48.976289, 2.273825, 2.242064, '2016-01-06 14:47:45', '2016-01-06 14:47:45'),
            array('23620a82-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Arcs', 'ChIJtccPrPmtzhIRXIJGDS20oXI', 6.478446, 43.462876, 43.501788, 43.4048449, 6.538443, 6.433868, '2015-11-22 19:40:45', '2015-11-22 19:40:45'),
            array('237c7b1d-4f0b-11e6-84b7-c8cbb8b4347f', 'Vincennes', 'ChIJTcXqm6Ny5kcRQDeLaMOCCwQ', 2.4394969, 48.847759, 48.853358, 48.840891, 2.4579331, 2.418554, '2015-11-22 18:15:32', '2015-11-22 18:15:32'),
            array('23a2d9bc-4f0b-11e6-84b7-c8cbb8b4347f', 'Meylan', 'ChIJTfhyrr_1ikcRh2Yf_Xu9-K0', 5.777628, 45.209786, 45.241608, 45.186939, 5.820478, 5.7524741, '2015-11-22 18:14:21', '2015-11-22 18:14:21'),
            array('23bb8cab-4f0b-11e6-84b7-c8cbb8b4347f', 'Le Vésinet', 'ChIJtQt7q_Fi5kcR8DWMaMOCCwQ', 2.134716, 48.894198, 48.9096759, 48.8758649, 2.150463, 2.11201, '2015-11-22 18:36:33', '2015-11-22 18:36:33'),
            array('23d10d81-4f0b-11e6-84b7-c8cbb8b4347f', 'Sainte-Geneviève-des-Bois', 'ChIJtSy0gT3Z5UcRoEGLaMOCCwQ', 2.3259213, 48.6408416, 48.6657949, 48.6201009, 2.356407, 2.297278, '2015-11-22 19:21:20', '2015-11-22 19:21:20'),
            array('23e72537-4f0b-11e6-84b7-c8cbb8b4347f', 'Combloux', 'ChIJtWJKrG79i0cR0KG65CqrCAQ', 6.641306, 45.896937, 45.91525, 45.860603, 6.6832601, 6.5890829, '2015-11-26 22:08:59', '2015-11-26 22:08:59'),
            array('23fbdab8-4f0b-11e6-84b7-c8cbb8b4347f', 'Vitré', 'ChIJtXcIZWEoCUgR0KPkNs2lDAQ', -1.21543, 48.124746, 48.146289, 48.074685, -1.144932, -1.2495389, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('2412c51b-4f0b-11e6-84b7-c8cbb8b4347f', 'Mérignac', 'ChIJtyOAutvZVA0Rdh6B3PW9RFY', -0.656358, 44.8448769, 44.862704, 44.7997919, -0.606831, -0.7570631, '2015-11-22 18:12:26', '2015-11-22 18:12:26'),
            array('24281a1b-4f0b-11e6-84b7-c8cbb8b4347f', 'Neuilly-sur-Seine', 'ChIJtZbjc2Nl5kcRl7My9Zx1Odw', 2.26851, 48.884831, 48.8979689, 48.8740669, 2.28459, 2.2461396, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('24414567-4f0b-11e6-84b7-c8cbb8b4347f', 'Basel', 'ChIJTzBpJ8dJkEcRkIpt83DrHDY', 7.5885761, 47.5595986, 47.58992, 47.51931, 7.6341001, 7.5548199, '2016-02-10 10:47:50', '2016-02-10 10:47:50'),
            array('2455f718-4f0b-11e6-84b7-c8cbb8b4347f', 'Charenton-le-Pont', 'ChIJU_JmF1dy5kcRkDmLaMOCCwQ', 2.4162805, 48.8193107, 48.829534, 48.81618, 2.419942, 2.3908925, '2015-11-22 20:32:19', '2015-11-22 20:32:19'),
            array('246bac02-4f0b-11e6-84b7-c8cbb8b4347f', 'Embrun', 'ChIJU-r1VkahzBIRUKiX_aUZCAQ', 6.495865, 44.564164, 44.619546, 44.541601, 6.5195339, 6.4305429, '2015-11-22 20:42:14', '2015-11-22 20:42:14'),
            array('248150d5-4f0b-11e6-84b7-c8cbb8b4347f', 'Noisy-le-Grand', 'ChIJU1IiZQ0O5kcRMDuLaMOCCwQ', 2.55261, 48.848579, 48.8580269, 48.807248, 2.596305, 2.515572, '2015-11-22 18:56:12', '2015-11-22 18:56:12'),
            array('24958a8a-4f0b-11e6-84b7-c8cbb8b4347f', 'Fougères', 'ChIJU1l5_Xg3CUgRB9bsuoqTMWU', -1.204626, 48.3515609, 48.3743879, 48.328069, -1.172128, -1.2185649, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('24afe10d-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Deux Alpes', 'ChIJu26ygcESikcRCXyyB2KEqTM', 6.124414, 45.012284, null, null, null, null, '2015-11-22 16:09:48', '2015-11-22 16:09:48'),
            array('24c4c2c4-4f0b-11e6-84b7-c8cbb8b4347f', 'Guebwiller', 'ChIJU2KlCsN-kUcRHu9iXGHTi7E', 7.209336, 47.911586, 47.927365, 47.89632, 7.2421461, 7.1774121, '2015-11-22 19:07:39', '2015-11-22 19:07:39'),
            array('24db5f49-4f0b-11e6-84b7-c8cbb8b4347f', '92000', 'ChIJu2Sd14dk5kcR0IDY4caCCxw', 2.2071125, 48.8941822, 48.9205726, 48.8742071, 2.2343695, 2.1687844, '2016-01-20 17:21:21', '2016-01-20 17:21:21'),
            array('24effda3-4f0b-11e6-84b7-c8cbb8b4347f', 'Tignes', 'ChIJu359KGMMiUcRsKi65CqrCAQ', 6.9055785, 45.4683226, 45.531779, 45.4170539, 7.056126, 6.8592231, '2015-11-22 17:41:23', '2015-11-22 17:41:23'),
            array('2505d117-4f0b-11e6-84b7-c8cbb8b4347f', 'Route du Collet d\'Allevard', 'ChIJubgsfmZMikcR_ThJ1uCx4Bo', 6.0830634, 45.3940622, 45.397583, 45.3905886, 6.0865588, 6.079993, '2016-01-26 20:26:51', '2016-01-26 20:26:51'),
            array('251b2ac4-4f0b-11e6-84b7-c8cbb8b4347f', 'Guillaumes', 'ChIJUc_3Wb1bzBIRmhVGnU1bUu8', 6.853764, 44.089224, 44.172469, 44.028844, 6.9432079, 6.791371, '2015-11-22 19:05:32', '2015-11-22 19:05:32'),
            array('2530b850-4f0b-11e6-84b7-c8cbb8b4347f', 'Laure-Minervois', 'ChIJUcs8BxzXsRIRkCdtFiGIBwQ', 2.5201159, 43.270216, 43.31001, 43.2255819, 2.567197, 2.4616199, '2016-01-21 20:45:20', '2016-01-21 20:45:20'),
            array('25469085-4f0b-11e6-84b7-c8cbb8b4347f', 'Chevaigné', 'ChIJUeq04rzcDkgRMucjsa7c18U', -1.630622, 48.211236, 48.2544319, 48.195725, -1.6202009, -1.6571418, '2016-05-11 11:01:23', '2016-05-11 11:01:23'),
            array('255c3449-4f0b-11e6-84b7-c8cbb8b4347f', 'Château-Bernard', 'ChIJufhmud2aikcRcH2-5CqrCAQ', 5.576166, 44.974535, 45.004935, 44.9609219, 5.60316, 5.5267551, '2015-11-22 18:22:47', '2015-11-22 18:22:47'),
            array('2573edb6-4f0b-11e6-84b7-c8cbb8b4347f', 'Achères', 'ChIJUQcHldth5kcRkEWMaMOCCwQ', 2.0700219, 48.960796, 48.989189, 48.946294, 2.1782816, 2.0501959, '2016-02-28 22:11:08', '2016-02-28 22:11:08'),
            array('2587c7c7-4f0b-11e6-84b7-c8cbb8b4347f', 'Wittenheim', 'ChIJUQFFw9SCkUcRICo5mrlfCgQ', 7.339439, 47.81077, 47.8314379, 47.791383, 7.3554231, 7.2617861, '2015-11-22 19:07:39', '2015-11-22 19:07:39'),
            array('259d7841-4f0b-11e6-84b7-c8cbb8b4347f', 'Dunkirk', 'ChIJuSD_2W2L3EcRoG1kgT7xCgQ', 2.3767763, 51.0343684, 51.057893, 50.975899, 2.4464731, 2.239696, '2016-01-28 17:32:51', '2016-01-28 17:32:51'),
            array('25b2fffa-4f0b-11e6-84b7-c8cbb8b4347f', 'Gières', 'ChIJuUe0GHv1ikcRAuxmkyZxKfQ', 5.791127, 45.1800489, 45.2063989, 45.15914, 5.8130401, 5.766477, '2015-11-22 16:34:36', '2015-11-22 16:34:36'),
            array('25c8d79c-4f0b-11e6-84b7-c8cbb8b4347f', 'Oloron-Sainte-Marie', 'ChIJuY1s7qyqVw0REJYTSBdlBgQ', -0.605292, 43.194413, 43.249993, 43.0857978, -0.501025, -0.6896771, '2015-11-22 18:19:26', '2015-11-22 18:19:26'),
            array('25de6339-4f0b-11e6-84b7-c8cbb8b4347f', 'Brides-les-Bains', 'ChIJv_I2XzN-iUcRWpILloqk5rE', 6.571562, 45.45441, 45.4614339, 45.4478839, 6.596333, 6.5463481, '2015-11-22 19:18:57', '2015-11-22 19:18:57'),
            array('25f64871-4f0b-11e6-84b7-c8cbb8b4347f', 'Norway', 'ChIJv-VNj0VoEkYRK9BkuJ07sKE', 8.468946, 60.472024, 71.1854261, 57.9595951, 31.1682684, 4.5000963, '2015-12-02 15:31:44', '2015-12-02 15:31:44'),
            array('260d1932-4f0b-11e6-84b7-c8cbb8b4347f', 'Brison-Saint-Innocent', 'ChIJv0dO-LgKi0cRYLa65CqrCAQ', 5.889265, 45.720705, 45.767034, 45.713565, 5.911442, 5.8702842, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('2622799f-4f0b-11e6-84b7-c8cbb8b4347f', 'Alpe d\'Huez Tourisme', 'ChIJV0Y23a9rikcRDq0lA1fu5lo', 6.0650537, 45.0909293, null, null, null, null, '2016-01-30 18:56:32', '2016-01-30 18:56:32'),
            array('26398803-4f0b-11e6-84b7-c8cbb8b4347f', 'Barcelonnette', 'ChIJV5MuKpmRzBIRvmwPJ9MJ-Lw', 6.650463, 44.3863, 44.4128799, 44.3373629, 6.6737369, 6.6189299, '2015-11-22 18:26:03', '2015-11-22 18:26:03'),
            array('264eff57-4f0b-11e6-84b7-c8cbb8b4347f', 'Lamorlaye', 'ChIJv6xKzjlG5kcRgDpkgT7xCgQ', 2.439851, 49.157554, 49.1762039, 49.144563, 2.482668, 2.3695909, '2015-11-22 18:09:40', '2015-11-22 18:09:40'),
            array('266567ab-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Carroz d\'Arâches', 'ChIJV9ZrN4YAjEcRgFcogy2rCAo', 6.6378806, 46.0256156, null, null, null, null, '2016-04-28 23:55:04', '2016-04-28 23:55:04'),
            array('267c59b2-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Jouan-des-Guérets', 'ChIJVb4zZRWEDkgRUKjkNs2lDAQ', -1.972348, 48.5990279, 48.62187, 48.5866399, -1.924889, -2.0085988, '2015-11-22 19:01:39', '2015-11-22 19:01:39'),
            array('2692e62f-4f0b-11e6-84b7-c8cbb8b4347f', 'Saintes', 'ChIJVba31Ff9AEgRFP9wHIbH71Q', -0.633389, 45.744175, 45.789079, 45.703327, -0.5840918, -0.705334, '2016-05-02 14:08:04', '2016-05-02 14:08:04'),
            array('26aa01c7-4f0b-11e6-84b7-c8cbb8b4347f', 'Courbevoie', 'ChIJvccvsApl5kcRED6LaMOCCwQ', 2.25929, 48.900552, 48.908269, 48.887075, 2.2837402, 2.230553, '2015-11-22 18:15:32', '2015-11-22 18:15:32'),
            array('26c1b74f-4f0b-11e6-84b7-c8cbb8b4347f', 'Bagneux', 'ChIJve_xYN1w5kcRlQX7pYZxqCY', 2.31002, 48.796696, 48.811124, 48.7848709, 2.326339, 2.2922061, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('26d963e7-4f0b-11e6-84b7-c8cbb8b4347f', 'Blagnac', 'ChIJVeJ4cDGlrhIRWaeXjdYatv4', 1.39703, 43.635087, 43.66653, 43.615376, 1.4066929, 1.3473589, '2015-11-22 19:41:27', '2015-11-22 19:41:27'),
            array('26ef42a5-4f0b-11e6-84b7-c8cbb8b4347f', 'Prades-le-Lez', 'ChIJvQnqYKypthIRig3OXEebQBc', 3.862765, 43.69798, 43.733894, 43.68197, 3.8834549, 3.846049, '2015-11-22 18:17:27', '2015-11-22 18:17:27'),
            array('27057298-4f0b-11e6-84b7-c8cbb8b4347f', 'Hossegor', 'ChIJVRfk-ZdbUQ0RsT1kMVaJZM4', -1.3976871, 43.6646192, 43.683658, 43.652262, -1.3705341, -1.4461564, '2015-11-26 08:46:58', '2015-11-26 08:46:58'),
            array('271b9ea4-4f0b-11e6-84b7-c8cbb8b4347f', 'La Frette', 'ChIJVRwjERlRikcREKAogy2rCAo', 5.942191, 45.3391469, null, null, null, null, '2015-11-22 18:22:47', '2015-11-22 18:22:47'),
            array('2731f899-4f0b-11e6-84b7-c8cbb8b4347f', 'Meillonnas', 'ChIJVRWNAn6yjEcRkRQxjcnsn6U', 5.351351, 46.245743, 46.2707409, 46.2195869, 5.3737741, 5.2955811, '2015-11-22 19:14:52', '2015-11-22 19:14:52'),
            array('27559a79-4f0b-11e6-84b7-c8cbb8b4347f', 'Bry-sur-Marne', 'ChIJVTR7JJMN5kcRwDmLaMOCCwQ', 2.5236489, 48.834913, 48.851289, 48.8272058, 2.540707, 2.5038991, '2016-03-14 15:38:59', '2016-03-14 15:38:59'),
            array('276c434a-4f0b-11e6-84b7-c8cbb8b4347f', 'Dinard', 'ChIJVVqqTcCADkgR48BvH6Pvhd8', -2.055125, 48.633024, 48.6544949, 48.603546, -2.029685, -2.0906989, '2015-11-22 19:01:39', '2015-11-22 19:01:39'),
            array('2784ba01-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Jean-d\'Aulps', 'ChIJVywC7BEcjEcRVyWikF7pHgA', 6.655654, 46.234581, 46.268378, 46.188341, 6.73025, 6.598543, '2016-03-15 19:01:55', '2016-03-15 19:01:55'),
            array('279d2098-4f0b-11e6-84b7-c8cbb8b4347f', 'Bidarray', 'ChIJVYXANGkvUQ0REKgTSBdlBgQ', -1.350543, 43.265573, 43.304279, 43.2321919, -1.2928591, -1.413188, '2015-11-22 20:51:03', '2015-11-22 20:51:03'),
            array('27b303f5-4f0b-11e6-84b7-c8cbb8b4347f', 'Enghien-les-Bains', 'ChIJVZ9jTIxo5kcRgDOLaMOCCwQ', 2.3034849, 48.9708759, 48.979467, 48.961742, 2.316982, 2.2929081, '2016-01-06 14:47:45', '2016-01-06 14:47:45'),
            array('27c89ad6-4f0b-11e6-84b7-c8cbb8b4347f', 'Najran', 'ChIJVZGPyAfr_hURhfSUiBJ5p9E', 44.2289441, 17.5656036, 17.6004128, 17.4746589, 44.2933307, 44.0930794, '2016-03-15 18:08:17', '2016-03-15 18:08:17'),
            array('27de4e32-4f0b-11e6-84b7-c8cbb8b4347f', 'Tours', 'ChIJVZqXSrPV_EcRMIQ4BdfIDQQ', 0.68484, 47.394144, 47.4395929, 47.348942, 0.737095, 0.6527849, '2015-11-22 19:53:10', '2015-11-22 19:53:10'),
            array('27f50e05-4f0b-11e6-84b7-c8cbb8b4347f', 'Laxou', 'ChIJw_IH_o2ilEcRwAjAtmyOOIM', 6.152525, 48.684256, 48.7006299, 48.6672289, 6.162605, 6.073859, '2015-11-22 18:18:55', '2015-11-22 18:18:55'),
            array('280b0e29-4f0b-11e6-84b7-c8cbb8b4347f', 'Breil-sur-Roya', 'ChIJw3r2-5aWzRIRgJ6X_aUZCAQ', 7.513929, 43.937465, 44.000983, 43.8749769, 7.573949, 7.4252599, '2015-11-22 19:10:32', '2015-11-22 19:10:32'),
            array('2820027e-4f0b-11e6-84b7-c8cbb8b4347f', 'Bagnères-de-Luchon', 'ChIJW4v2BPFhqBIRieXuPl7Ofjg', 0.592943, 42.788963, 42.809339, 42.6893299, 0.6822569, 0.570056, '2015-11-22 18:28:32', '2015-11-22 18:28:32'),
            array('2835dee1-4f0b-11e6-84b7-c8cbb8b4347f', 'Tarbes', 'ChIJw5TxvIrTqRIRSUfM7jTuv0c', 0.078082, 43.232951, 43.264987, 43.212478, 0.0930789, 0.037492, '2016-01-20 10:37:19', '2016-01-20 10:37:19'),
            array('284b649e-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Romain-de-Popey', 'ChIJW6zthzRg9EcRi7QaVlruaKI', 4.5306531, 45.8472404, 45.8782151, 45.831745, 4.5625891, 4.495542, '2015-11-22 19:42:43', '2015-11-22 19:42:43'),
            array('28611be5-4f0b-11e6-84b7-c8cbb8b4347f', '93000', 'ChIJw91yn8Ns5kcR4IHY4caCCxw', 2.422169, 48.9100302, 48.9193556, 48.8951585, 2.4762014, 2.4101326, '2016-01-18 15:27:25', '2016-01-18 15:27:25'),
            array('2875c936-4f0b-11e6-84b7-c8cbb8b4347f', 'Bobigny', 'ChIJw91yn8Ns5kcRUDyLaMOCCwQ', 2.439712, 48.908612, 48.9194379, 48.8952, 2.47619, 2.41011, '2016-01-18 16:12:13', '2016-01-18 16:12:13'),
            array('288ba5f4-4f0b-11e6-84b7-c8cbb8b4347f', 'Grand lac de Laffrey', 'ChIJW9I8NbdiikcRuSGgAfZNqQ0', 5.7785944, 45.01169, 45.023795, 44.9995294, 5.7824781, 5.77216, '2016-07-04 11:16:40', '2016-07-04 11:16:40'),
            array('28a0faeb-4f0b-11e6-84b7-c8cbb8b4347f', 'Strasbourg', 'ChIJwbIYXknIlkcRHyTnGDFIGpc', 7.7521113, 48.5734053, 48.646222, 48.4920049, 7.8360451, 7.6881429, '2016-01-20 11:43:23', '2016-01-20 11:43:23'),
            array('28b70588-4f0b-11e6-84b7-c8cbb8b4347f', 'Lézignan-Corbières', 'ChIJWc0FGj_KsRIRvqsI_v7R_d4', 2.7577759, 43.200503, 43.241274, 43.1553959, 2.8072089, 2.722634, '2015-12-04 10:19:50', '2015-12-04 10:19:50'),
            array('28cc5499-4f0b-11e6-84b7-c8cbb8b4347f', 'Orsinval', 'ChIJWdAi556MwkcRFlZijcMfU5k', 3.6349, 50.273224, 50.281872, 50.263185, 3.651024, 3.609629, '2015-11-22 18:00:29', '2015-11-22 18:00:29'),
            array('28e1b29b-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Martin-d\'Hères', 'ChIJWfqz2hD1ikcREGq-5CqrCAQ', 5.767761, 45.165133, 45.197351, 45.15914, 5.7862391, 5.7428, '2015-11-22 17:37:44', '2015-11-22 17:37:44'),
            array('28f8541d-4f0b-11e6-84b7-c8cbb8b4347f', 'Aix-en-Provence', 'ChIJWRK5BKONyRIRo4i2yL5TuVw', 5.447427, 43.529742, 43.62598, 43.445968, 5.5063649, 5.269529, '2015-11-22 18:23:42', '2015-11-22 18:23:42'),
            array('290e9409-4f0b-11e6-84b7-c8cbb8b4347f', 'Cadolive', 'ChIJwRU6VA-XyRIRUASX_aUZCAQ', 5.544298, 43.396004, 43.4060889, 43.3825069, 5.5562698, 5.504421, '2016-01-11 14:47:47', '2016-01-11 14:47:47'),
            array('29248bc6-4f0b-11e6-84b7-c8cbb8b4347f', '71000', 'ChIJwSB4yT1u80cR0P1HGTjOCRw', 4.8274692, 46.3280068, 46.3797584, 46.2545363, 4.8586377, 4.7575112, '2016-01-18 18:06:39', '2016-01-18 18:06:39'),
            array('293a4af5-4f0b-11e6-84b7-c8cbb8b4347f', 'Ancelle', 'ChIJWTQsj4xEyxIRrJy1wKi9KWE', 6.205691, 44.623591, 44.64898, 44.590076, 6.3020508, 6.155623, '2015-11-22 16:06:22', '2015-11-22 16:06:22'),
            array('294fed27-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Gervais', 'ChIJWW0uLODqikcR8Gu-5CqrCAQ', 5.481683, 45.2017809, 45.212291, 45.1638559, 5.5276891, 5.463761, '2015-11-27 15:10:41', '2015-11-27 15:10:41'),
            array('296555dd-4f0b-11e6-84b7-c8cbb8b4347f', 'Laval', 'ChIJwWtZ_On9CEgR4BYNHlI3DQQ', -0.7669906, 48.0785146, 48.0911449, 48.0244169, -0.717261, -0.8209399, '2016-06-23 06:06:43', '2016-06-23 06:06:43'),
            array('297aba88-4f0b-11e6-84b7-c8cbb8b4347f', 'Argenteuil', 'ChIJWwXL32Rm5kcR0DaLaMOCCwQ', 2.2466847, 48.9472096, 48.972417, 48.9281733, 2.2919194, 2.203301, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('299514ad-4f0b-11e6-84b7-c8cbb8b4347f', 'Pont-sur-Yonne', 'ChIJX08cvAwU70cRsFsNszTOCQQ', 3.2039909, 48.2878029, 48.3061799, 48.255263, 3.2219801, 3.1549321, '2015-11-22 18:05:44', '2015-11-22 18:05:44'),
            array('29b30c35-4f0b-11e6-84b7-c8cbb8b4347f', 'Station Chalmazel', 'ChIJX0cnJLqC9kcRZPVXxuWnpJo', 3.8270128, 45.6767866, null, null, null, null, '2016-03-21 19:34:54', '2016-03-21 19:34:54'),
            array('29d3c539-4f0b-11e6-84b7-c8cbb8b4347f', 'Ax-les-Thermes', 'ChIJX4w2DRp4rxIRsGFDL5z2BgQ', 1.837545, 42.720254, 42.728513, 42.6398039, 1.8654269, 1.77285, '2015-11-22 20:27:45', '2015-11-22 20:27:45'),
            array('29ee5052-4f0b-11e6-84b7-c8cbb8b4347f', 'Bourgoin-Jallieu', 'ChIJX685-ncui0cRoH--5CqrCAQ', 5.27212, 45.597108, 45.6362661, 45.568813, 5.32359, 5.225849, '2015-11-22 19:02:09', '2015-11-22 19:02:09'),
            array('2a048c72-4f0b-11e6-84b7-c8cbb8b4347f', 'Ville-la-Grand', 'ChIJx7xVHxRujEcREJW65CqrCAQ', 6.2471439, 46.2016149, 46.2153538, 46.1968709, 6.2858851, 6.23004, '2015-11-22 17:36:09', '2015-11-22 17:36:09'),
            array('2a1ad571-4f0b-11e6-84b7-c8cbb8b4347f', 'Valenton', 'ChIJXaGjIhcL5kcRwHwBEVBm2dw', 2.459965, 48.749736, 48.7718381, 48.727635, 2.4846121, 2.435304, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('2a31458f-4f0b-11e6-84b7-c8cbb8b4347f', 'St-Malo', 'ChIJXb8mIRCBDkgRLeErsq196fg', -2.025674, 48.649337, 48.6943769, 48.598187, -1.9366429, -2.076663, '2015-11-22 19:01:39', '2015-11-22 19:01:39'),
            array('2a478cd6-4f0b-11e6-84b7-c8cbb8b4347f', 'Istres', 'ChIJxbAOzsQEthIRoAKX_aUZCAQ', 4.987968, 43.513006, 43.6229789, 43.467974, 5.015516, 4.879768, '2015-11-22 18:30:48', '2015-11-22 18:30:48'),
            array('2a5ce6aa-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Mathieu-de-Tréviers', 'ChIJXbc7yKsDtBIR4L1qFiGIBwQ', 3.8757119, 43.7731223, 43.7985359, 43.737426, 3.9018309, 3.8237359, '2015-11-22 18:17:27', '2015-11-22 18:17:27'),
            array('2a731e6d-4f0b-11e6-84b7-c8cbb8b4347f', 'Cognac', 'ChIJxbTAFw3zAEgRoLvuYJLTBQQ', -0.328744, 45.691046, 45.717377, 45.67471, -0.3016969, -0.3735509, '2016-05-02 14:08:04', '2016-05-02 14:08:04'),
            array('2a894490-4f0b-11e6-84b7-c8cbb8b4347f', 'Sarlat-la-Canéda', 'ChIJXc36PhFWqxIRcPQXSBdlBgQ', 1.217292, 44.890891, 44.962439, 44.8495909, 1.2652129, 1.133596, '2016-03-19 15:24:13', '2016-03-19 15:24:13'),
            array('2a9f1bc1-4f0b-11e6-84b7-c8cbb8b4347f', 'Blois', 'ChIJXdLII5VX40cRsCk4BdfIDQQ', 1.3359475, 47.5860921, 47.6209599, 47.541759, 1.3556321, 1.254101, '2015-12-15 11:18:20', '2015-12-15 11:18:20'),
            array('2ab64ce2-4f0b-11e6-84b7-c8cbb8b4347f', 'Puteaux', 'ChIJxfUaLBpl5kcR4MryedU_O9g', 2.23964, 48.884748, 48.895651, 48.8705569, 2.2539732, 2.222433, '2015-11-22 18:08:55', '2015-11-22 18:08:55'),
            array('2accb1f8-4f0b-11e6-84b7-c8cbb8b4347f', 'Tourcoing', 'ChIJXScKvtQow0cRj4WtfX0xLiU', 3.16207, 50.724993, 50.7490289, 50.693048, 3.196742, 3.118607, '2015-11-22 18:13:08', '2015-11-22 18:13:08'),
            array('2ae38b4b-4f0b-11e6-84b7-c8cbb8b4347f', 'Accous', 'ChIJxURS0cOgVw0RQK8TSBdlBgQ', -0.599055, 42.974682, 42.993888, 42.8531129, -0.4924161, -0.6626501, '2015-11-22 18:19:26', '2015-11-22 18:19:26'),
            array('2b00d478-4f0b-11e6-84b7-c8cbb8b4347f', 'Pertuis', 'ChIJXVFsth4nyhIRUOF0eo4ju-U', 5.501843, 43.694275, 43.727569, 43.658718, 5.6237519, 5.445166, '2016-03-23 14:41:40', '2016-03-23 14:41:40'),
            array('2b1889c7-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Maurice', 'ChIJXViiV9dy5kcR4DeLaMOCCwQ', 2.423655, 48.8205669, 48.824187, 48.8126911, 2.4654831, 2.419606, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('2b2eabf8-4f0b-11e6-84b7-c8cbb8b4347f', 'Issy-les-Moulineaux', 'ChIJxy7Ltot65kcRoSduGnfQJos', 2.2743419, 48.8245306, 48.8346259, 48.813305, 2.2893901, 2.2382247, '2016-03-21 15:19:42', '2016-03-21 15:19:42'),
            array('2b463614-4f0b-11e6-84b7-c8cbb8b4347f', 'Valence', 'ChIJxyEyxplX9UcRgLW_5CqrCAQ', 4.89236, 44.933393, 44.959722, 44.88686, 4.97839, 4.854378, '2015-11-22 19:49:27', '2015-11-22 19:49:27'),
            array('2b5d20b4-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Didier-au-Mont-d\'Or', 'ChIJXyVom6aU9EcRgB275CqrCAQ', 4.79802, 45.811833, 45.842842, 45.789003, 4.818425, 4.7821809, '2016-02-29 09:34:10', '2016-02-29 09:34:10'),
            array('2b72f85f-4f0b-11e6-84b7-c8cbb8b4347f', 'Vicdessos', 'ChIJXZdpgR5CrxIRUE9DL5z2BgQ', 1.499298, 42.768258, 42.7834469, 42.7631089, 1.5401409, 1.4714989, '2015-11-22 19:09:13', '2015-11-22 19:09:13'),
            array('2b8a064f-4f0b-11e6-84b7-c8cbb8b4347f', 'Caunes-Minervois', 'ChIJXznY7QrZsRIR4C5tFiGIBwQ', 2.5281994, 43.3270136, 43.3805489, 43.3032308, 2.5590129, 2.4767309, '2016-01-21 20:45:20', '2016-01-21 20:45:20'),
            array('2ba0e8ef-4f0b-11e6-84b7-c8cbb8b4347f', 'La Côte-d\'Aime', 'ChIJY_evbJJjiUcRYLS65CqrCAQ', 6.6685739, 45.567979, 45.655856, 45.556826, 6.689421, 6.636339, '2015-11-22 20:40:30', '2015-11-22 20:40:30'),
            array('2bb736c9-4f0b-11e6-84b7-c8cbb8b4347f', 'Treffort-Cuisiat', 'ChIJY-0GdE6yjEcRPIOnOGaVOLY', 5.3685605, 46.2846752, 46.3155881, 46.230403, 5.4036751, 5.3025761, '2015-11-22 19:14:52', '2015-11-22 19:14:52'),
            array('2bced474-4f0b-11e6-84b7-c8cbb8b4347f', 'Bagnères-de-Bigorre', 'ChIJY-O7E98kqBIRIIo9L5z2BgQ', 0.1491049, 43.065093, 43.092171, 42.866268, 0.225757, 0.042269, '2015-11-22 18:23:23', '2015-11-22 18:23:23'),
            array('2be5e1a1-4f0b-11e6-84b7-c8cbb8b4347f', 'Chambéry', 'ChIJY19pCFeoi0cRnIk9ps8awtU', 5.917781, 45.564601, 45.61643, 45.548009, 5.941446, 5.8712609, '2015-11-22 18:06:28', '2015-11-22 18:06:28'),
            array('2bfce777-4f0b-11e6-84b7-c8cbb8b4347f', 'Albens', 'ChIJY2ncBLSfi0cRt8SPcRpTs-g', 5.943959, 45.785606, 45.8146379, 45.7587281, 5.963054, 5.907576, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('2c139bd6-4f0b-11e6-84b7-c8cbb8b4347f', 'Montagne Noire', 'ChIJY4z53_gXrhIRcfPdpG7dZG8', 2.4621053, 43.424678, null, null, null, null, '2016-01-15 15:13:16', '2016-01-15 15:13:16'),
            array('2c2890cb-4f0b-11e6-84b7-c8cbb8b4347f', 'Morzine', 'ChIJy6Ruql0djEcRKPtwOv_w3pQ', 6.708877, 46.179192, 46.2060839, 46.137925, 6.8070485, 6.6793691, '2015-11-22 17:40:06', '2015-11-22 17:40:06'),
            array('2c3fa842-4f0b-11e6-84b7-c8cbb8b4347f', 'Vesoul', 'ChIJY796U4vykkcRAPcOszTOCQQ', 6.15428, 47.619788, 47.6518189, 47.612798, 6.1742911, 6.1302781, '2016-02-10 11:00:20', '2016-02-10 11:00:20'),
            array('2c553d91-4f0b-11e6-84b7-c8cbb8b4347f', '73550', 'ChIJy82H7Q2BiUcR8IXkQS6rCBw', 6.5859564, 45.3638493, 45.4535827, 45.2745367, 6.6647338, 6.5206096, '2016-03-01 23:31:57', '2016-03-01 23:31:57'),
            array('2c6b80c0-4f0b-11e6-84b7-c8cbb8b4347f', 'Station de ski La Norma', 'ChIJyc-Jj7uRiUcRlWUhyC5Xnac', 6.696401, 45.200863, null, null, null, null, '2015-12-24 15:42:58', '2015-12-24 15:42:58'),
            array('2c80d23c-4f0b-11e6-84b7-c8cbb8b4347f', 'Cagnes-sur-Mer', 'ChIJyckd0aLTzRIRBmbJMvxJQ1w', 7.14882, 43.663739, 43.700549, 43.6418859, 7.179691, 7.1206549, '2015-11-22 17:47:14', '2015-11-22 17:47:14'),
            array('2c96a184-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Martin-Bellevue', 'ChIJycwj8KmIi0cRcJi65CqrCAQ', 6.148198, 45.96286, 45.994769, 45.952423, 6.168848, 6.1045871, '2015-11-22 19:03:43', '2015-11-22 19:03:43'),
            array('2cac7ec5-4f0b-11e6-84b7-c8cbb8b4347f', 'Limoges', 'ChIJYQRo5q80-UcREU02WTFKvhA', 1.261105, 45.833619, 45.9286071, 45.7886751, 1.317546, 1.1462491, '2016-02-02 17:46:29', '2016-02-02 17:46:29'),
            array('2cc2596d-4f0b-11e6-84b7-c8cbb8b4347f', 'Marquay', 'ChIJyTgcSYxTqxIRQAQYSBdlBgQ', 1.134647, 44.944531, 44.975164, 44.908184, 1.177311, 1.079925, '2015-11-22 17:44:34', '2015-11-22 17:44:34'),
            array('2cd764dd-4f0b-11e6-84b7-c8cbb8b4347f', 'Boulogne-Billancourt', 'ChIJYUevEOh65kcRkD6LaMOCCwQ', 2.2399123, 48.8396952, 48.8531587, 48.8217911, 2.2616607, 2.2239064, '2015-11-22 19:00:59', '2015-11-22 19:00:59'),
            array('2cedfac2-4f0b-11e6-84b7-c8cbb8b4347f', 'Annecy', 'ChIJyVEFHPqPi0cRujQFYoEWeEI', 6.129384, 45.899247, 45.930329, 45.849431, 6.1460336, 6.101075, '2015-11-22 17:46:29', '2015-11-22 17:46:29'),
            array('2d04ff1c-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Denis', 'ChIJYW0056pu5kcRDeko-brr78c', 2.357443, 48.936181, 48.952109, 48.9014819, 2.39814, 2.3336986, '2015-11-22 19:09:13', '2015-11-22 19:09:13'),
            array('2d1abaf5-4f0b-11e6-84b7-c8cbb8b4347f', 'Switzerland', 'ChIJYW1Zb-9kjEcRFXvLDxG1Vlw', 8.227512, 46.818188, 47.8084545, 45.8179199, 10.4923401, 5.9560801, '2016-05-19 16:54:28', '2016-05-19 16:54:28'),
            array('2d300e12-4f0b-11e6-84b7-c8cbb8b4347f', 'Monteils', 'ChIJywT0F4RGtBIRWHPp6-e6i8I', 4.180996, 44.089326, 44.1022209, 44.07326, 4.203797, 4.1608929, '2015-11-22 20:43:14', '2015-11-22 20:43:14'),
            array('2d461d3f-4f0b-11e6-84b7-c8cbb8b4347f', 'Panossas', 'ChIJYX2VqtDM9EcRwHG-5CqrCAQ', 5.204104, 45.67884, 45.6947091, 45.659704, 5.215591, 5.16477, '2015-11-22 20:33:59', '2015-11-22 20:33:59'),
            array('2d5aab41-4f0b-11e6-84b7-c8cbb8b4347f', 'Aubagne', 'ChIJyyal8DOjyRIRvMKMseEhjSw', 5.5676425, 43.2926781, 43.336297, 43.2443139, 5.615394, 5.5131519, '2015-11-22 18:23:42', '2015-11-22 18:23:42'),
            array('2d88c467-4f0b-11e6-84b7-c8cbb8b4347f', 'Vercors Massif', 'ChIJYyjaFaWbikcRVXlPsSGfM4U', 5.5300643, 44.9676086, null, null, null, null, '2015-11-22 16:36:04', '2015-11-22 16:36:04'),
            array('2da01825-4f0b-11e6-84b7-c8cbb8b4347f', 'Demi-Quartier', 'ChIJYzpc1D79i0cRqesQciRdaZo', 6.6330857, 45.8783335, 45.887138, 45.858504, 6.66319, 6.5942151, '2015-11-22 16:11:55', '2015-11-22 16:11:55'),
            array('2db58257-4f0b-11e6-84b7-c8cbb8b4347f', 'Oyonnax', 'ChIJz_BcIXi9jEcRsAjC5CqrCAQ', 5.655335, 46.257773, 46.2995158, 46.2203139, 5.713308, 5.5901881, '2015-11-22 19:14:52', '2015-11-22 19:14:52'),
            array('2dcaea38-4f0b-11e6-84b7-c8cbb8b4347f', 'Brussels', 'ChIJZ2jHc-2kw0cRpwJzeGY6i8E', 4.3517103, 50.8503396, 50.91371, 50.79624, 4.43698, 4.3138, '2016-01-22 15:05:09', '2016-01-22 15:05:09'),
            array('2de05253-4f0b-11e6-84b7-c8cbb8b4347f', 'Marignane', 'ChIJz2Y1kSLmyRIRMAKX_aUZCAQ', 5.218332, 43.4212739, 43.45108, 43.392552, 5.263954, 5.171651, '2015-11-22 19:46:36', '2015-11-22 19:46:36'),
            array('2df6a690-4f0b-11e6-84b7-c8cbb8b4347f', 'Méribel-Mottaret', 'ChIJZ2ZJJweBiUcRUMErgy2rCAo', 6.580031, 45.372927, null, null, null, null, '2016-02-18 06:54:41', '2016-02-18 06:54:41'),
            array('2e0c8275-4f0b-11e6-84b7-c8cbb8b4347f', 'L\'Isle-sur-la-Sorgue', 'ChIJz6W3I-71tRIRbeHX0dHl1R0', 5.057106, 43.91419, 43.9646259, 43.873513, 5.1050169, 5.0124829, '2016-03-23 14:41:40', '2016-03-23 14:41:40'),
            array('2e2228e9-4f0b-11e6-84b7-c8cbb8b4347f', 'Uzès', 'ChIJZ7QNZ0C1tRIR8DNrFiGIBwQ', 4.419946, 44.01211, 44.07671, 43.981097, 4.446855, 4.385212, '2015-11-22 17:39:09', '2015-11-22 17:39:09'),
            array('2e38d12e-4f0b-11e6-84b7-c8cbb8b4347f', 'Colmar', 'ChIJz8Fw9t9lkUcRZulsYT5-XeM', 7.358512, 48.0793589, 48.1823189, 48.040906, 7.4692721, 7.315536, '2015-11-22 19:07:39', '2015-11-22 19:07:39'),
            array('2e504a90-4f0b-11e6-84b7-c8cbb8b4347f', 'Moûtiers', 'ChIJz8xcedrVi0cRuOgl0sM117g', 6.529926, 45.484615, 45.4973219, 45.4760469, 6.553793, 6.52189, '2015-11-22 20:40:30', '2015-11-22 20:40:30'),
            array('2e66e9be-4f0b-11e6-84b7-c8cbb8b4347f', 'Yerres', 'ChIJz9ID2EQK5kcRhk3IZGPav4I', 2.490967, 48.712858, 48.735881, 48.6898289, 2.5213231, 2.465433, '2015-11-22 20:41:30', '2015-11-22 20:41:30'),
            array('2e7d171e-4f0b-11e6-84b7-c8cbb8b4347f', 'Miramas', 'ChIJza-aYrADthIRQFNhmV8_0cw', 5.002136, 43.588896, 43.6180909, 43.550191, 5.062742, 4.966944, '2015-12-10 21:32:41', '2015-12-10 21:32:41'),
            array('2e92646f-4f0b-11e6-84b7-c8cbb8b4347f', 'Les Angles', 'ChIJzbOhmEfqtRIR8EdrFiGIBwQ', 4.77358, 43.954465, 43.9662269, 43.916658, 4.7918979, 4.720762, '2015-11-22 17:40:06', '2015-11-22 17:40:06'),
            array('2ea84248-4f0b-11e6-84b7-c8cbb8b4347f', 'Mazamet', 'ChIJZQGOttAYrhIRDeh2fhwTS0s', 2.376579, 43.490103, 43.5166119, 43.416556, 2.4740379, 2.298534, '2016-01-20 18:37:45', '2016-01-20 18:37:45'),
            array('2ec2401e-4f0b-11e6-84b7-c8cbb8b4347f', 'Arles', 'ChIJzRRIXQdythIRUAWX_aUZCAQ', 4.6277769, 43.676647, 43.760423, 43.328714, 4.8763449, 4.426191, '2016-03-23 14:41:40', '2016-03-23 14:41:40'),
            array('2ed841c9-4f0b-11e6-84b7-c8cbb8b4347f', 'Thann', 'ChIJzSWJz1sqkkcRkCw5mrlfCgQ', 7.102382, 47.810702, 47.8390251, 47.7935219, 7.1226301, 7.050849, '2015-11-22 20:50:18', '2015-11-22 20:50:18'),
            array('2eef36b2-4f0b-11e6-84b7-c8cbb8b4347f', 'Saint-Laurent-de-Mure', 'ChIJzTZd5w7P9EcR4Be75CqrCAQ', 5.045359, 45.686106, 45.707968, 45.6603869, 5.1084471, 5.010985, '2015-11-22 20:33:59', '2015-11-22 20:33:59'),
            array('2f05d55c-4f0b-11e6-84b7-c8cbb8b4347f', 'Rochefort', 'ChIJZzAyDS4Fi0cRkK265CqrCAQ', 5.72125, 45.582048, 45.59469, 45.5654419, 5.7331131, 5.68502, '2016-01-18 09:45:54', '2016-01-18 09:45:54'),
            array('2f1bf771-4f0b-11e6-84b7-c8cbb8b4347f', 'Nancy', 'ChIJzZKmF26YlEcRjUmCDbFx1k4', 6.184417, 48.692054, 48.7092969, 48.666865, 6.212633, 6.1342411, '2015-11-22 18:18:55', '2015-11-22 18:18:55'),
            array('fc007214-4f0a-11e6-84b7-c8cbb8b4347f', 'Cannes', 'ChIJ__8MU4CBzhIRIJ6X_aUZCAQ', 7.017369, 43.552847, 43.5748511, 43.499718, 7.0742039, 6.9447059, '2015-11-22 20:43:14', '2015-11-22 20:43:14'),
            array('fc15109f-4f0a-11e6-84b7-c8cbb8b4347f', 'Montrouge', 'ChIJ_1dbg_9w5kcRcD2LaMOCCwQ', 2.317384, 48.816363, 48.822288, 48.809588, 2.332443, 2.30008, '2015-11-22 18:37:34', '2015-11-22 18:37:34'),
            array('fc28a8ed-4f0a-11e6-84b7-c8cbb8b4347f', 'Toulouse', 'ChIJ_1J17G-7rhIRMBBBL5z2BgQ', 1.444209, 43.604652, 43.6686919, 43.532708, 1.515354, 1.3503279, '2015-11-22 19:09:13', '2015-11-22 19:09:13'),
            array('fc3b1219-4f0a-11e6-84b7-c8cbb8b4347f', 'Seyssel', 'ChIJ_1L9T8J7i0cRMJe65CqrCAQ', 5.8507466, 45.9592788, 45.992218, 45.932015, 5.8810151, 5.8256181, '2015-11-22 19:02:54', '2015-11-22 19:02:54'),
            array('fc4e7523-4f0a-11e6-84b7-c8cbb8b4347f', 'Pugny-Chatenod', 'ChIJ_2iMzhehi0cR4K265CqrCAQ', 5.953589, 45.695263, 45.7033499, 45.6823538, 5.9804381, 5.934636, '2016-05-08 19:16:07', '2016-05-08 19:16:07'),
            array('fc60f6b2-4f0a-11e6-84b7-c8cbb8b4347f', 'Romainville', 'ChIJ_3QEYCFt5kcRAODXvADyUy0', 2.435048, 48.886112, 48.89838, 48.871307, 2.454191, 2.424082, '2015-11-22 18:15:32', '2015-11-22 18:15:32'),
            array('fc745b91-4f0a-11e6-84b7-c8cbb8b4347f', 'Maisons-Alfort', 'ChIJ_4g5IjBz5kcRXZkNCvALux8', 2.429443, 48.801148, 48.816878, 48.786595, 2.4639931, 2.4159061, '2015-11-22 20:32:19', '2015-11-22 20:32:19'),
            array('fc87d6ba-4f0a-11e6-84b7-c8cbb8b4347f', 'Margaux', 'ChIJ_8QWIyXUAUgRcOAWSBdlBgQ', -0.677519, 45.042035, 45.0610969, 45.0319179, -0.6439508, -0.6974939, '2016-03-06 08:51:36', '2016-03-06 08:51:36'),
            array('fc9a2b91-4f0a-11e6-84b7-c8cbb8b4347f', 'Le Touvet', 'ChIJ_94RuBJRikcREGW-5CqrCAQ', 5.950642, 45.358854, 45.373091, 45.329986, 5.9764271, 5.9198311, '2015-11-22 18:06:28', '2015-11-22 18:06:28'),
            array('fcad82d1-4f0a-11e6-84b7-c8cbb8b4347f', 'Toulon', 'ChIJ_bI1ewIbyRIRMMiP_aUZCAQ', 5.928, 43.124228, 43.171673, 43.101049, 5.987383, 5.8794789, '2016-01-19 16:44:43', '2016-01-19 16:44:43'),
            array('fcbfd6b8-4f0a-11e6-84b7-c8cbb8b4347f', 'Huez', 'ChIJ_QWckgBrikcRsHe-5CqrCAQ', 6.057819, 45.081164, 45.123944, 45.070562, 6.120984, 6.0415801, '2015-11-22 15:35:46', '2015-11-22 15:35:46'),
            array('fcd3f3e9-4f0a-11e6-84b7-c8cbb8b4347f', 'Béthune', 'ChIJ_xmTA1ki3UcRGH3GgV_c-cI', 2.63926, 50.531036, 50.550589, 50.5093069, 2.671622, 2.6159161, '2016-01-29 18:38:23', '2016-01-29 18:38:23'),
            array('fce7242c-4f0a-11e6-84b7-c8cbb8b4347f', 'Mougins', 'ChIJ_Yc_GowpzBIR5q3_lcFyYmk', 7.006491, 43.6023319, 43.618248, 43.5678848, 7.054593, 6.95459, '2015-11-22 17:55:24', '2015-11-22 17:55:24'),
            array('fcfa2ece-4f0a-11e6-84b7-c8cbb8b4347f', 'Perpignan', 'ChIJ_Yj9gE5usBIRT93yIGUwgxw', 2.8948332, 42.6886591, 42.7488459, 42.649256, 2.9827209, 2.826282, '2016-02-10 15:13:56', '2016-02-10 15:13:56'),
            array('fd0d8105-4f0a-11e6-84b7-c8cbb8b4347f', 'Liège', 'ChIJ-_ysjkv3wEcRQGtNL6uZAAQ', 5.5796662, 50.6325574, 50.6881899, 50.5610901, 5.6751101, 5.5230701, '2016-01-22 16:29:30', '2016-01-22 16:29:30'),
            array('fd203e0c-4f0a-11e6-84b7-c8cbb8b4347f', 'Grasse', 'ChIJ--1YOf4lzBIRLS74jJgndxE', 6.926492, 43.660153, 43.698676, 43.613249, 6.9881079, 6.8859, '2015-11-22 19:10:32', '2015-11-22 19:10:32'),
            array('fd335ee4-4f0a-11e6-84b7-c8cbb8b4347f', 'Prades', 'ChIJ--nx6TP8rxIRwIkd95SzmaU', 2.423231, 42.617718, 42.634113, 42.5872229, 2.4536929, 2.401492, '2016-01-05 16:10:12', '2016-01-05 16:10:12'),
            array('fd460adc-4f0a-11e6-84b7-c8cbb8b4347f', 'Libourne', 'ChIJ-wZwFk9JVQ0R8OEWSBdlBgQ', -0.243985, 44.912998, 44.9521629, 44.872827, -0.2024061, -0.2631151, '2015-11-22 18:21:21', '2015-11-22 18:21:21'),
            array('fd58f0b3-4f0a-11e6-84b7-c8cbb8b4347f', 'Saint-Gély-du-Fesc', 'ChIJ-X9CUTOqthIRIL9qFiGIBwQ', 3.804436, 43.69208, 43.714575, 43.6645699, 3.840283, 3.779706, '2015-11-22 18:17:27', '2015-11-22 18:17:27'),
            array('fd6dedc0-4f0a-11e6-84b7-c8cbb8b4347f', 'Saint-Hilaire', 'ChIJ-Z7wvG5XikcRoGu-5CqrCAQ', 5.8782004, 45.3085676, 45.3308459, 45.293573, 5.905854, 5.8553499, '2016-01-26 20:26:51', '2016-01-26 20:26:51'),
            array('fd814df0-4f0a-11e6-84b7-c8cbb8b4347f', 'Beaune', 'ChIJ-z9YzUTz8kcRU_8-ZJb0jLo', 4.840004, 47.02603, 47.0589021, 46.9955299, 4.89736, 4.786901, '2016-01-22 11:07:46', '2016-01-22 11:07:46'),
            array('fd9495f6-4f0a-11e6-84b7-c8cbb8b4347f', 'Levallois-Perret', 'ChIJ0_88t4Nv5kcRsD2LaMOCCwQ', 2.287864, 48.893217, 48.9031447, 48.8857149, 2.303756, 2.2714537, '2015-11-22 18:11:55', '2015-11-22 18:11:55'),
            array('fda7afa0-4f0a-11e6-84b7-c8cbb8b4347f', 'Allemond', 'ChIJ0-PqIVNCikcRkIK-5CqrCAQ', 6.040608, 45.13216, 45.2391159, 45.116282, 6.0778451, 5.959116, '2015-11-22 18:27:00', '2015-11-22 18:27:00'),
            array('fdbbf601-4f0a-11e6-84b7-c8cbb8b4347f', 'Carcès', 'ChIJ075irTNbyRIR3cyonpORY4Y', 6.181635, 43.4761098, 43.505207, 43.4423189, 6.235329, 6.115227, '2016-01-22 14:55:36', '2016-01-22 14:55:36'),
            array('fdd0310f-4f0a-11e6-84b7-c8cbb8b4347f', 'Aix-les-Bains', 'ChIJ09K5naKgi0cRQLm65CqrCAQ', 5.908998, 45.692341, 45.723683, 45.664681, 5.935786, 5.88333, '2015-11-22 17:45:22', '2015-11-22 17:45:22'),
            array('fde3559b-4f0a-11e6-84b7-c8cbb8b4347f', 'Megève', 'ChIJ0alOYJHii0cRsJy65CqrCAQ', 6.61775, 45.856876, 45.879654, 45.7996779, 6.682354, 6.565939, '2015-11-22 17:50:12', '2015-11-22 17:50:12'),
            array('fdfb4346-4f0a-11e6-84b7-c8cbb8b4347f', 'Creil', 'ChIJ0ekK8vhJ5kcREEVkgT7xCgQ', 2.4783913, 49.2576872, 49.2745029, 49.236745, 2.515101, 2.453271, '2016-02-08 10:34:53', '2016-02-08 10:34:53'),
            array('fe0ee108-4f0a-11e6-84b7-c8cbb8b4347f', 'Évian-les-Bains', 'ChIJ0y-p-UwjjEcR0J-65CqrCAQ', 6.590949, 46.401488, 46.403311, 46.3787809, 6.614174, 6.5680171, '2016-01-27 16:56:23', '2016-01-27 16:56:23'),
            array('fe29429b-4f0a-11e6-84b7-c8cbb8b4347f', 'Sainte-Maxime', 'ChIJ0ZhSiZq5zhIRkMmP_aUZCAQ', 6.640482, 43.310184, 43.4209269, 43.295842, 6.687268, 6.5405919, '2016-01-19 14:01:24', '2016-01-19 14:01:24'),
            array('fe432086-4f0a-11e6-84b7-c8cbb8b4347f', 'Viuz-en-Sallaz', 'ChIJ1__ByJgNjEcRsJS65CqrCAQ', 6.4101609, 46.1472759, 46.1926288, 46.1381039, 6.4504659, 6.3650631, '2015-11-22 17:36:09', '2015-11-22 17:36:09'),
            array('fe5feb8c-4f0a-11e6-84b7-c8cbb8b4347f', 'Compiègne', 'ChIJ1-3LIebV50cREEZkgT7xCgQ', 2.826145, 49.417816, 49.434413, 49.366788, 2.930606, 2.7791091, '2015-11-22 18:09:40', '2015-11-22 18:09:40'),
            array('fe72d994-4f0a-11e6-84b7-c8cbb8b4347f', 'Le Coudray-Montceaux', 'ChIJ13Hjn0Tm5UcR0EeLaMOCCwQ', 2.485767, 48.565903, 48.580981, 48.5401, 2.531288, 2.45379, '2015-11-22 17:53:03', '2015-11-22 17:53:03'),
            array('fe8679bc-4f0a-11e6-84b7-c8cbb8b4347f', 'Lac de Monteynard-Avignonet', 'ChIJ1ev662eAikcRHYmIQpzdKSA', 5.6808578, 44.8834411, 44.907575, 44.8646431, 5.7589621, 5.672377, '2016-07-04 11:16:40', '2016-07-04 11:16:40'),
            array('fe994d93-4f0a-11e6-84b7-c8cbb8b4347f', 'Thiais', 'ChIJ1fhlHWx05kcRsDeLaMOCCwQ', 2.387405, 48.760344, 48.774788, 48.7461089, 2.4060011, 2.36777, '2016-06-04 13:51:12', '2016-06-04 13:51:12'),
            array('feabd2ce-4f0a-11e6-84b7-c8cbb8b4347f', 'Gap', 'ChIJ1ROi2Hg_yxIRYKeX_aUZCAQ', 6.079758, 44.559638, 44.664568, 44.494774, 6.141088, 5.9820799, '2015-11-22 17:43:58', '2015-11-22 17:43:58'),
            array('febd8e1a-4f0a-11e6-84b7-c8cbb8b4347f', 'Apt', 'ChIJ1S6Q-RAWyhIR8MaP_aUZCAQ', 5.395439, 43.876452, 43.9211709, 43.841799, 5.456368, 5.3063729, '2015-11-22 19:45:12', '2015-11-22 19:45:12'),
            array('fecfea62-4f0a-11e6-84b7-c8cbb8b4347f', 'La Plagne', 'ChIJ1U6MSI97iUcRtOqkoHFFnyI', 6.659294, 45.509874, null, null, null, null, '2015-12-03 16:44:24', '2015-12-03 16:44:24'),
            array('fee4c369-4f0a-11e6-84b7-c8cbb8b4347f', 'Méribel', 'ChIJ20mMtDuAiUcRMMErgy2rCAo', 6.56436, 45.397528, null, null, null, null, '2015-11-22 17:43:18', '2015-11-22 17:43:18'),
            array('fef822eb-4f0a-11e6-84b7-c8cbb8b4347f', 'Deuil-la-Barre', 'ChIJ21MFZ_xo5kcRwDOLaMOCCwQ', 2.3272339, 48.975751, 48.9832728, 48.957522, 2.3441881, 2.309709, '2016-01-06 14:47:45', '2016-01-06 14:47:45'),
            array('ff0a8ec1-4f0a-11e6-84b7-c8cbb8b4347f', 'L\'Alpe d\'Huez', 'ChIJ22xshQZrikcRq8cxNU7lY54', 6.068348, 45.092624, null, null, null, null, '2015-11-22 14:35:27', '2015-11-22 14:35:27'),
            array('ff1c9ef1-4f0a-11e6-84b7-c8cbb8b4347f', 'La Motte-Servolex', 'ChIJ29A7WvkHi0cRtFQb6ppRFJY', 5.874112, 45.596488, 45.642448, 45.5650039, 5.893167, 5.814875, '2015-11-22 18:01:07', '2015-11-22 18:01:07'),
            array('ff2f32fc-4f0a-11e6-84b7-c8cbb8b4347f', 'Yvelines', 'ChIJ2c8x_zyc5kcRYCqLaMOCCwM', 1.8256572, 48.7850939, 49.085448, 48.4385569, 2.229127, 1.4461701, '2016-01-18 12:39:54', '2016-01-18 12:39:54'),
            array('ff40e5ee-4f0a-11e6-84b7-c8cbb8b4347f', 'Villeneuve-Minervois', 'ChIJ2chv3wnYsRIREB095S3OvsY', 2.4621719, 43.315288, 43.37065, 43.295467, 2.51015, 2.4302579, '2016-01-21 20:45:20', '2016-01-21 20:45:20'),
            array('ff532ab4-4f0a-11e6-84b7-c8cbb8b4347f', 'Chaville', 'ChIJ2xbSLIR75kcRcJwU02XpC1U', 2.192418, 48.808026, 48.8209129, 48.797225, 2.206029, 2.1758701, '2015-11-22 20:48:44', '2015-11-22 20:48:44'),
            array('ff69252d-4f0a-11e6-84b7-c8cbb8b4347f', 'La Chapelle-d\'Abondance', 'ChIJ3_RK80mgjkcRp_FHJvH57Zo', 6.788024, 46.29583, 46.3471022, 46.2610838, 6.8537521, 6.749407, '2016-02-04 15:20:58', '2016-02-04 15:20:58'),
            array('ff7bded0-4f0a-11e6-84b7-c8cbb8b4347f', 'Créteil', 'ChIJ33ICM68M5kcRZ9RFVDG5c0U', 2.455572, 48.790367, 48.807349, 48.7617289, 2.477349, 2.42727, '2015-11-22 18:37:34', '2015-11-22 18:37:34'),
            array('ff8ebb78-4f0a-11e6-84b7-c8cbb8b4347f', 'Royan', 'ChIJ33kHxkJ2AUgR4XPOWCqaafw', -1.043182, 45.623027, 45.656633, 45.612009, -0.970831, -1.054461, '2016-04-14 18:33:00', '2016-04-14 18:33:00'),
            array('ffa1b254-4f0a-11e6-84b7-c8cbb8b4347f', 'Marcq-en-Barœul', 'ChIJ33qpSSkqw0cR1vYdv-Jeeqg', 3.096267, 50.670368, 50.710782, 50.647805, 3.132286, 3.0688741, '2015-11-22 19:22:16', '2015-11-22 19:22:16'),
            array('ffb435d7-4f0a-11e6-84b7-c8cbb8b4347f', 'Saint-Pierre', 'ChIJ34pc_LiggiERqLU7NXG--b4', 55.471843, -21.332838, null, null, null, null, '2015-11-22 19:09:13', '2015-11-22 19:09:13'),
            array('ffc7e80a-4f0a-11e6-84b7-c8cbb8b4347f', 'Clichy', 'ChIJ37pZhAxv5kcRy2sTsHOkXXA', 2.304768, 48.904526, 48.9133415, 48.89415, 2.320847, 2.2880486, '2016-01-04 16:17:45', '2016-01-04 16:17:45'),
            array('ffda3448-4f0a-11e6-84b7-c8cbb8b4347f', 'Vandœuvre-lès-Nancy', 'ChIJ3a_p4nWilEcRoOw6mrlfCgQ', 6.17376, 48.661118, 48.677393, 48.6426491, 6.1968501, 6.123655, '2015-11-22 18:18:55', '2015-11-22 18:18:55'),
            array('ffee5550-4f0a-11e6-84b7-c8cbb8b4347f', 'La Défense', 'ChIJ3dGtqgVl5kcRiXgs6GzIh_E', 2.2418428, 48.8897359, 48.8951866, 48.8868069, 2.2525775, 2.2341533, '2016-01-04 16:17:45', '2016-01-04 16:17:45')
        );
        $this->getEm()->getConnection()->beginTransaction();
        foreach ($cities as $city) {
            $this->getEm()->getConnection()->insert(
                'city_city',
                array(
                    'city_id' => $city[0],
                    'title' => $city[1],
                    'googleId' => $city[2],
                    'lng' => $city[3],
                    'lat' => $city[4],
                    'north' => $city[5],
                    'south' => $city[6],
                    'east' => $city[7],
                    'west' => $city[8],
                    'createdAt' => $city[9],
                    'updatedAt' => $city[10],
                )
            );
        }
        $this->getEm()->getConnection()->commit();
        $this->setCities(
            array_map(function ($city) {
                return array($city[2] => $city[0]);
            }, $cities)
        );

        $this->logger->debug('import.table.city.insert.query.finished');

        return $this->getCities();
    }

    /**
     * Gets the value of em.
     *
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Sets the value of em.
     *
     * @param mixed $em the em
     *
     * @return self
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * Gets the value of connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the value of connection.
     *
     * @param mixed $connection the connection
     *
     * @return self
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Gets the value of logger.
     *
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the value of logger.
     *
     * @param mixed $logger the logger
     *
     * @return self
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Gets the value of regexHelper.
     *
     * @return mixed
     */
    public function getRegexHelper()
    {
        return $this->regexHelper;
    }

    /**
     * Sets the value of regexHelper.
     *
     * @param mixed $regexHelper the regex helper
     *
     * @return self
     */
    public function setRegexHelper(RegexHelper $regexHelper)
    {
        $this->regexHelper = $regexHelper;

        return $this;
    }

    /**
     * Gets the value of cities.
     *
     * @return mixed
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Sets the value of cities.
     *
     * @param mixed $cities the cities
     *
     * @return self
     */
    protected function addCity($key, $value)
    {
        if (!isset($this->cities[$key])) {
            $this->cities[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets the value of cities.
     *
     * @param mixed $cities the cities
     *
     * @return self
     */
    protected function setCities($cities)
    {
        $this->cities = $cities;

        return $this;
    }
}
