<?php
/**
 * Created by PhpStorm.
 * User: maxence
 * Date: 28/05/2016
 * Time: 07:59
 */

namespace Mdespeuilles\LanguageBundle\Entity\Traits;


trait LanguageEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Mdespeuilles\LanguageBundle\Entity\Language", cascade={"persist"})
     */
    private $language;

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }
}
