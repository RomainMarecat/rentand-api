<?php

namespace App\Services;

class Params
{

    public function getLocales()
    {
        return array('fr', 'en');
    }

    public function getAges()
    {
        return array(
            1 => array(
                'fr' => 'Enfants',
                'en' => 'Children'),
            2 => array(
                'fr' => 'Adolescents',
                'en' => 'Teenagers'),
            3 => array(
                'fr' => 'Adultes',
                'en' => 'Adults'),
            4 => array(
                'fr' => 'Séniors',
                'en' => 'Senior'),
        );
    }

    public function getLevels()
    {
        return array(
            1 => array(
                'fr' => 'Débutant',
                'en' => 'Beginner'),
            2 => array(
                'fr' => 'Intermédiaire',
                'en' => 'Intermediate'),
            3 => array(
                'fr' => 'Avancé',
                'en' => 'Advanced'),
            4 => array(
                'fr' => 'Expert',
                'en' => 'Expert'),
        );
    }

    public function getPassions()
    {
        return array(
            1 => array(
                'fr' => 'Partir à l’aventure',
                'en' => 'Partir à l’aventure'),
            2 => array(
                'fr' => 'Chanter sous la douche',
                'en' => 'Chanter sous la douche'),
            3 => array(
                'fr' => 'Chasser les pokemon',
                'en' => 'Chasser les pokemon'),
            4 => array(
                'fr' => 'Danser toute la nuit',
                'en' => 'Danser toute la nuit'),
            5 => array(
                'fr' => 'Bouquiner au coin du feu',
                'en' => 'Bouquiner au coin du feu'),
            6 => array(
                'fr' => 'M\'imaginer en rockstar',
                'en' => 'M\'imaginer en rockstar'),
            7 => array(
                'fr' => 'Rêver',
                'en' => 'Rêver'),
            8 => array(
                'fr' => 'La fondue savoyarde',
                'en' => 'La fondue savoyarde'),
            9 => array(
                'fr' => 'Manger des pommes',
                'en' => 'Manger des pommes'),
            10 => array(
                'fr' => 'Les apéros',
                'en' => 'Les apéros'),
            11 => array(
                'fr' => 'Construire des maisons',
                'en' => 'Construire des maisons'),
            12 => array(
                'fr' => 'Les grosses cylindrées',
                'en' => 'Les grosses cylindrées'),
            13 => array(
                'fr' => 'Murmurer à l\'oreille des animaux',
                'en' => 'Murmurer à l\'oreille des animaux'),
            14 => array(
                'fr' => 'Jouer au poker',
                'en' => 'Jouer au poker'),
            15 => array(
                'fr' => 'Lire des livres sans images',
                'en' => 'Lire des livres sans images'),
            16 => array(
                'fr' => 'Collectionner les BD',
                'en' => 'Collectionner les BD'),
            17 => array(
                'fr' => 'Ma couette, le chocolat et Netflix',
                'en' => 'Ma couette, le chocolat et Netflix'),
            18 => array(
                'fr' => 'Les films à l\'eau de rose',
                'en' => 'Les films à l\'eau de rose'),

        );
    }

    public function getTitles()
    {
        return array(
            0 => array(
                'fr' => '',
                'en' => '',
            ),
            1 => array(
                'fr' => 'Moniteur de ski',
                'en' => 'Ski instructor',
            ),
            2 => array(
                'fr' => 'Monitrice de ski',
                'en' => 'Ski instructor',
            ),
            3 => array(
                'fr' => 'Guide de haute montagne',
                'en' => 'Mountaineering guide',
            ),
            4 => array(
                'fr' => 'Moniteur de snowboard',
                'en' => 'Snowboard instructor',
            ),
            5 => array(
                'fr' => 'Monitrice de snowboard',
                'en' => 'Snowboard instructor',
            ),
            6 => array(
                'fr' => 'Moniteur de ski de fond',
                'en' => 'Nordic instructor',
            ),
            7 => array(
                'fr' => 'Monitrice de ski de fond',
                'en' => 'Nordic instructor',
            ),
            8 => array(
                'fr' => 'Accompagnateur en montagne',
                'en' => 'Mountaineering guide',
            ),
            9 => array(
                'fr' => 'Accompagnatrice en montagne',
                'en' => 'Mountaineering guide',
            ),
            10 => array(
                'fr' => 'Moniteur de télémark',
                'en' => ' Telemark instructor',
            ),
            11 => array(
                'fr' => 'Monitrice de télémark',
                'en' => 'Telemark instructor',
            ),
            12 => array(
                'fr' => 'Entraineur de biathlon',
                'en' => 'Biathlon trainer',
            ),
            13 => array(
                'fr' => 'Musher',
                'en' => 'Musher',
            ),
            14 => array(
                'fr' => 'Moniteur de vol libre',
                'en' => 'Free fly instructor',
            ),
            15 => array(
                'fr' => 'Monitrice de vol libre',
                'en' => 'Free fly instructor',
            ),
            16 => array(
                'fr' => 'Moniteur de pilotage',
                'en' => 'Driving instructor',
            ),
            17 => array(
                'fr' => 'Monitrice de pilotage',
                'en' => 'Driving instructor',
            ),
            18 => array(
                'fr' => 'Moniteur de vélo',
                'en' => 'Cycling instructor',
            ),
            19 => array(
                'fr' => 'Monitrice de vélo',
                'en' => 'Cycling instructor',
            ),
            20 => array(
                'fr' => 'Moniteur de luge',
                'en' => 'Sledge instructor',
            ),
            21 => array(
                'fr' => 'Monitrice de luge',
                'en' => 'Sledge instructor',
            ),
            22 => array(
                'fr' => 'Moniteur de bobsleigh',
                'en' => 'Bobsleigh instructor',
            ),
            23 => array(
                'fr' => 'Monitrice de bobsleigh',
                'en' => 'Bobsleigh instructor',
            ),
            24 => array(
                'fr' => 'Moniteur de VTT',
                'en' => 'MTB instructor',
            ),
            25 => array(
                'fr' => 'Monitrice de VTT',
                'en' => 'MTB instructor',
            ),
            26 => array(
                'fr' => 'Moniteur de BMX',
                'en' => 'BMX instructor',
            ),
            27 => array(
                'fr' => 'Monitrice de BMX',
                'en' => 'BMX instructor',
            ),
            28 => array(
                'fr' => 'Moniteur d\'escalade',
                'en' => 'Climbing instructor',
            ),
            29 => array(
                'fr' => 'Monitrice d\'escalade',
                'en' => 'Climbing instructor',
            ),
            30 => array(
                'fr' => 'Moniteur de spéléologie',
                'en' => 'Caving instructor',
            ),
            31 => array(
                'fr' => 'Monitrice de spéléologie',
                'en' => 'Caving instructor',
            ),
            32 => array(
                'fr' => 'Moniteur de chute libre',
                'en' => 'Free fall intructor',
            ),
            33 => array(
                'fr' => 'Monitrice de chute libre',
                'en' => 'Free fall intructor',
            ),
            34 => array(
                'fr' => 'Coach de trail',
                'en' => 'Trail coach',
            ),
            35 => array(
                'fr' => 'Entraineur de course d\'orientation',
                'en' => 'Orienteering trainer',
            ),
            36 => array(
                'fr' => 'Moniteur de mountain board',
                'en' => 'Mountain board instructor',
            ),
            37 => array(
                'fr' => 'Monitrice de mountain board',
                'en' => 'Mountain board instructor',
            ),
            38 => array(
                'fr' => 'Moniteur de paddle',
                'en' => 'Paddle instructor',
            ),
            39 => array(
                'fr' => 'Monitrice de paddle',
                'en' => 'Paddle instructor',
            ),
            40 => array(
                'fr' => 'Moniteur d\'aviron',
                'en' => 'Rowing instructor',
            ),
            41 => array(
                'fr' => 'Monitrice d\'aviron',
                'en' => 'Rowing instructor',
            ),
            42 => array(
                'fr' => 'Moniteur de rafting',
                'en' => 'Rafting instructor',
            ),
            43 => array(
                'fr' => 'Monitrice de rafting',
                'en' => 'Rafting instructor',
            ),
            44 => array(
                'fr' => 'Moniteur canoë-kayak',
                'en' => 'Canoe-Kayak instructor',
            ),
            45 => array(
                'fr' => 'Monitrice canoë-kayak',
                'en' => 'Canoe-Kayak instructor',
            ),
            46 => array(
                'fr' => 'Moniteur de voile',
                'en' => 'Sailing instructor',
            ),
            47 => array(
                'fr' => 'Monitrice de voile',
                'en' => 'Sailing instructor',
            ),
            48 => array(
                'fr' => 'Moniteur de kitesurf',
                'en' => 'Kitesurfing Instructor',
            ),
            49 => array(
                'fr' => 'Monitrice de kitesurf',
                'en' => 'Kitesurfing Instructor',
            ),
            50 => array(
                'fr' => 'Moniteur de ski nautique',
                'en' => 'Waterski instructor',
            ),
            51 => array(
                'fr' => 'Monitrice de ski nautique',
                'en' => 'Waterski instructor',
            ),
            52 => array(
                'fr' => 'Moniteur de wakeboard',
                'en' => 'Wakeboard instructor',
            ),
            53 => array(
                'fr' => 'Monitrice de wakeboard',
                'en' => 'Wakeboard instructor',
            ),
            54 => array(
                'fr' => 'Moniteur de wakesurf',
                'en' => 'Wakesurfing instructor',
            ),
            55 => array(
                'fr' => 'Monitrice de wakesurf',
                'en' => 'Wakesurfing instructor',
            ),
            56 => array(
                'fr' => 'Moniteur de surf',
                'en' => 'Surf instructor',
            ),
            57 => array(
                'fr' => 'Monitrice de surf',
                'en' => 'Surf instructor',
            ),
            58 => array(
                'fr' => 'Moniteur de plongée',
                'en' => 'Scuba diving instructor',
            ),
            59 => array(
                'fr' => 'Monitrice de plongée',
                'en' => 'Scuba diving instructor',
            ),
            60 => array(
                'fr' => 'Instructeur d\'apnée',
                'en' => 'Apnea instructor',
            ),
            61 => array(
                'fr' => 'Instructrice d\'apnée',
                'en' => 'Apnea instructor',
            ),
            62 => array(
                'fr' => 'Moniteur de canyoning',
                'en' => 'Canyoneering Instructor',
            ),
            63 => array(
                'fr' => 'Monitrice de canyoning',
                'en' => 'Canyoneering Instructor',
            ),
            64 => array(
                'fr' => 'Moniteur d\'hydrospeed',
                'en' => 'Hydrospeed instructor',
            ),
            65 => array(
                'fr' => 'Monitrice d\'hydrospeed',
                'en' => 'Hydrospeed instructor',
            ),
            66 => array(
                'fr' => 'Maître nageur',
                'en' => 'Swimming instructor',
            ),
            67 => array(
                'fr' => 'Entraineur de plongeon',
                'en' => 'Diving instructor',
            ),
            68 => array(
                'fr' => 'Moniteur de golf',
                'en' => 'Golf instructor',
            ),
            69 => array(
                'fr' => 'Monitrice de golf',
                'en' => 'Golf instructor',
            ),
            70 => array(
                'fr' => 'Moniteur d\'équitation',
                'en' => 'Horse riding instructor',
            ),
            71 => array(
                'fr' => 'Monitrice d\'équitation',
                'en' => 'Horse riding instructor',
            ),
            72 => array(
                'fr' => 'Prof de danse',
                'en' => 'Dance professor',
            ),
            73 => array(
                'fr' => 'Entraineur de rugby',
                'en' => 'Rugby coach',
            ),
            74 => array(
                'fr' => 'Entraineur de football',
                'en' => 'Football coach',
            ),
            75 => array(
                'fr' => 'Entraineur de Basketball',
                'en' => 'Basketball coach',
            ),
            76 => array(
                'fr' => 'Entraineur de handball',
                'en' => 'Handball coach',
            ),
            77 => array(
                'fr' => 'Entraineur de volley',
                'en' => 'Volleyball coach',
            ),
            78 => array(
                'fr' => 'Entraineur de Tennis de table',
                'en' => 'Table Tennis coach',
            ),
            79 => array(
                'fr' => 'Prof de Squash',
                'en' => 'Squash instructor',
            ),
            80 => array(
                'fr' => 'Prof de badminton',
                'en' => 'Badminton instructor',
            ),
            81 => array(
                'fr' => 'Prof de tennis',
                'en' => 'Tennis Instructor',
            ),
            82 => array(
                'fr' => 'Moniteur de quad',
                'en' => 'Quad bike instructor',
            ),
            83 => array(
                'fr' => 'Monitrice de quad',
                'en' => 'Quad bike instructor',
            ),
            84 => array(
                'fr' => 'Entraineur d\'athlétisme',
                'en' => 'Athletics instructor',
            ),
            85 => array(
                'fr' => 'Coach sportif',
                'en' => 'Sport coach',
            ),
            86 => array(
                'fr' => 'Entraineur d\'haltérophilie',
                'en' => 'Weightlifting coach',
            ),
            87 => array(
                'fr' => 'Coach de musculation',
                'en' => 'Bodybuilding coach',
            ),
            88 => array(
                'fr' => 'Coach cardio-boxing',
                'en' => 'Cardio-boxing coach',
            ),
            89 => array(
                'fr' => 'Prof d\'aquagym',
                'en' => 'Watergym coach',
            ),
            90 => array(
                'fr' => 'Prof d\'aquabike',
                'en' => 'Waterbike coach',
            ),
            91 => array(
                'fr' => 'Prof de Yoga',
                'en' => 'Yoga coach',
            ),
            92 => array(
                'fr' => 'Prof de boxe',
                'en' => 'Boxing instructor',
            ),
            93 => array(
                'fr' => 'Prof de judo',
                'en' => 'Judo instructor',
            ),
            94 => array(
                'fr' => 'Prof de karaté',
                'en' => 'Karate instructor',
            ),
            95 => array(
                'fr' => 'Prof de jujitzu',
                'en' => 'Jujitzu instructor',
            ),
            96 => array(
                'fr' => 'Instructeur Krav Maga',
                'en' => 'Krav Maga instructor',
            ),
            97 => array(
                'fr' => 'Instructrice Krav Maga',
                'en' => 'Krav Maga instructor',
            ),
            98 => array(
                'fr' => 'Prof de taekwendo',
                'en' => 'Taekwendo instructor',
            ),
            99 => array(
                'fr' => 'Maitre d\'armes',
                'en' => 'Fencing coach',
            )
        );
    }
}
