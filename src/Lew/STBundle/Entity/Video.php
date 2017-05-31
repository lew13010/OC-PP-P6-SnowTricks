<?php

namespace Lew\STBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="Lew\STBundle\Repository\VideoRepository")
 */
class Video
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=512, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="string", length=128, nullable=true)
     */
    private $platform;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=128, nullable=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="Lew\STBundle\Entity\Trick", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Video
     */
    public function setUrl($iframe = null)
    {
        $testYT = preg_match('/youtube/i', $iframe);
        $testDM = preg_match('/dailymotion/i', $iframe);

        if($testYT){
            $platform = 'youtube';
            $code = preg_split("/\"/",str_replace('embed/', '', stristr($iframe, 'embed/')))[0];
        }elseif($testDM){
            $platform = 'dailymotion';
            $code = preg_split("/\"/",str_replace('video/', '', stristr($iframe, 'video/')))[0];
        }else{
            $platform = null;
            $code = null;
        }

        if ($platform != null && $code != null){
            $this->setPlatform($platform);
            $this->setCode($code);
        }
        $this->url = $iframe;
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set trick
     *
     * @param \Lew\STBundle\Entity\Trick $trick
     *
     * @return Video
     */
    public function setTrick(\Lew\STBundle\Entity\Trick $trick)
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Get trick
     *
     * @return \Lew\STBundle\Entity\Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }

    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * Set platform
     *
     * @param string $platform
     *
     * @return Video
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Video
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
