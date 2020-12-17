<?php

namespace Easytranslate\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_translation_profile")
 */
class TranslationProfile extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_name", type="string", nullable=true)
     */
    private $profileName = null;

    /**
     * @var int
     * @ORM\Column(name="source_shop_id", type="integer")
     */
    protected $sourceShopId;

    /**
     * @var string
     * @ORM\Column(name="target_shop_ids", type="string", nullable=true)
     */
    protected $targetShopIds;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProfileName(): string
    {
        return $this->profileName;
    }

    /**
     * @param string $profileName
     */
    public function setProfileName(string $profileName): void
    {
        $this->profileName = $profileName;
    }

    /**
     * @return mixed
     */
    public function getSourceShopId()
    {
        return $this->sourceShopId;
    }

    /**
     * @param mixed $sourceShopId
     */
    public function setSourceShopId($sourceShopId): void
    {
        $this->sourceShopId = $sourceShopId;
    }

    /**
     * @return string
     */
    public function getTargetShopIds(): string
    {
        return $this->targetShopIds;
    }

    /**
     * @param string $targetShopIds
     */
    public function setTargetShopIds(string $targetShopIds): void
    {
        $this->targetShopIds = $targetShopIds;
    }

}
