<?php

namespace App\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Entity\Advert;
use Entity\Image;
use Helper\RegexHelper;

/**
 * Class ImageManager
 *
 * @author Romain Marecat <romain.marecat@gmail.com>
 */
class ImageManager
{
    protected $em;

    protected $connection;

    protected $logger;

    protected $regexHelper;

    protected $images = array();

    protected $users;

    public function createImage(array $imageV1)
    {
        $user = $this->getEm()->getRepository('App:Advert')->findOneById(
            $this->getAdvert($imageV1['advert_id'])
        );
        if ($user && $imageV1['filename'] != null) {
            $image = new Image();
            foreach ($imageV1 as $key => $value) {
                $setter = $this->getRegexHelper()->setCamelCase('set' . ucfirst($key));
                if (method_exists($image, $setter)) {
                    $image->$setter($value);

                    if (in_array($setter, array('setCreatedAt', 'setUpdatedAt'))) {
                        $image->$setter(new \DateTime($value));
                    }
                }
            }

            $image
                ->setExt(strtolower(explode('.', $imageV1['filename'])[1]))
                ->setAdvert($user)
                ->setFilename($user->getSlug())
                ->setAlt($imageV1['image_id'] . '.' . $imageV1['filename'])// ->setAlt($user->getSlug())
            ;

            return $image;
        }

        return false;
    }

    public function createAdvertSportImages(array $imageV1)
    {
        $user = $this->getEm()->getRepository('App:Advert')->findOneById(
            $this->getAdvert($imageV1['advert_id'])
        );

        $userSports = new ArrayCollection(
            $this->getEm()->getRepository('App:AdvertSport')->findByAdvert(
                $this->getAdvert($imageV1['advert_id'])
            )
        );

        $this->logger->info(
            'info sportimages',
            array(
                $user instanceof Advert ? $user->getId() . $user->getSlug() : null,
                $imageV1['filename'],

            )
        );
        if ($user && $imageV1['filename'] != null) {
            $i = 1;
            foreach ($userSports as $userSport) {
                $this->logger->info(
                    'info advert sport',
                    array(
                        $userSport->getId(),
                        $user->getSlug(),
                        $userSport->getSport()->getId()
                    )
                );
                $this->getEm()->getConnection()->beginTransaction();
                $image = new Image();
                foreach ($imageV1 as $key => $value) {
                    $setter = $this->getRegexHelper()->setCamelCase('set' . ucfirst($key));
                    if (method_exists($image, $setter)) {
                        $image->$setter($value);
                        if (in_array($setter, array('setCreatedAt', 'setUpdatedAt'))) {
                            $image->$setter(new \DateTime($value));
                        }
                    }
                }
                $slug = $user->getSlug() . '_' . $userSport->getSport()->getId();
                $nbImages = count($this->getEm()->getRepository('App:Media')->findByAdvertSport($userSport));
                if ($nbImages > 0) {
                    $slug = $user->getSlug() . '_' . $userSport->getSport()->getId()
                        . '_' . ($nbImages + 1);
                    $alt = $user->getSlug() . '_' . $userSport->getSport()->getId()
                        . '_' . ($nbImages + 1);
                } else {
                    $slug = $user->getSlug() . '_' . $userSport->getSport()->getId();
                    $alt = $user->getSlug() . '_' . $userSport->getSport()->getId();
                }

                $alt = $imageV1['id'] . "." . $imageV1['filename'];

                $image
                    ->setFilename($slug)
                    ->setAlt($alt)
                    ->setExt(strtolower(explode('.', $imageV1['filename'])[1]))
                    ->setAdvertSport($userSport);

                $this->getEm()->persist($image);
                $this->getEm()->flush();

                $this->getEm()->getConnection()->commit();

                $currentFilename = $image->getFilename() . '.' . $image->getExt();
                $this->addImage($imageV1['id'] . "." . $imageV1['filename'], $currentFilename);
                $i++;
            }
        }

        return $this;
    }

    public function registerAdvertSportImages()
    {
        $images = new \ArrayIterator(
            $this->getConnection()->fetchAll(
                'SELECT * FROM image im WHERE im.advert_id IS NOT NULL'
            )
        );
        while ($images->valid()) {
            try {
                $imageV1 = $images->current();
                $this->createAdvertSportImages($imageV1);
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.image.insert.query.error',
                    array(
                        'message' => $e->getMessage(),
                    )
                );
                throw $e;
            }
            $images->next();
        }

        return $this;
    }


    public function registerImages(array $users)
    {
        $this->setAdverts($users);
        $images = new \ArrayIterator(
            $this->getConnection()->fetchAll(
                'SELECT a.id as advert_id, u.id as user_id, im.id as image_id,
                    im.filename, im.ext, im.alt, im.created_at, im.updated_at
                FROM advert a
                LEFT JOIN user u ON a.user_id = u.id
                LEFT JOIN image im ON a.image_principale_id = im.id'
            )
        );
        $count = 0;
        $total = $images->count();
        $this->logger->info(
            'import.table.image.array.images',
            array(
                'total' => $total
            )
        );
        while ($images->valid()) {
            $this->getEm()->getConnection()->beginTransaction();
            try {
                $imageV1 = $images->current();
                $this->logger->info(
                    'imagesV1',
                    array(
                        'image' => $imageV1
                    )
                );
                $image = $this->createImage($imageV1);
                if ($image != false) {
                    $this->getEm()->persist($image);
                    $this->getEm()->flush();
                    $currentFilename = $image->getFilename() . '.' . $image->getExt();
                    $this->addImage($imageV1['image_id'] . "." . $imageV1['filename'], $currentFilename);
                }
                $this->getEm()->getConnection()->commit();
                $count++;
            } catch (\Exception $e) {
                if ($this->getEm()->getConnection()->getTransactionNestingLevel() != 0) {
                    $this->getEm()->getConnection()->rollBack(); // transaction marked for rollback only
                }
                $this->logger->error(
                    'import.table.image.insert.query.error',
                    array(
                        'message' => $e->getMessage(),
                    )
                );
                throw $e;
            }
            $images->next();
        }
        $this->logger->debug('import.table.image.insert.query.finished');

        $this->registerAdvertSportImages();

        return $this->getImages();
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
     * Gets the value of images.
     *
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Sets the value of images.
     *
     * @param mixed $images the images
     *
     * @return self
     */
    protected function addImage($key, $value)
    {
        $this->images[$key] = $value;

        return $this;
    }

    /**
     * Sets the value of images.
     *
     * @param mixed $images the images
     *
     * @return self
     */
    protected function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Gets the value of adverts.
     *
     * @return mixed
     */
    public function getAdverts()
    {
        return $this->adverts;
    }

    /**
     * Sets the value of adverts.
     *
     * @param mixed $users the adverts
     *
     * @return self
     */
    protected function getAdvert($key)
    {
        return isset($this->adverts[$key]) ? $this->adverts[$key] : null;
    }

    /**
     * Sets the value of adverts.
     *
     * @param mixed $users the adverts
     *
     * @return self
     */
    protected function setAdverts($users)
    {
        $this->adverts = $users;

        return $this;
    }
}
