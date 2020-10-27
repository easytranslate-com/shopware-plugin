<?php

namespace Easytranslate\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Shop\Shop;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_translation_task")
 */
class Task extends ModelEntity
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
     * @ORM\Column(name="task_id", type="string", nullable=true)
     */
    private $taskId;

    /**
     * @var
     * @ORM\Column(name="source_shop_id", type="integer")
     */
    protected $sourceShopId;

    /**
     * @var Shop $sourceShop
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="source_shop_id", referencedColumnName="id", nullable=true)
     */
    protected $sourceShop;

    /**
     * @var
     * @ORM\Column(name="target_shop_id", type="integer")
     */
    protected $targetShopId;

    /**
     * @var Shop $targetShop
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="target_shop_id", referencedColumnName="id", nullable=true)
     */
    protected $targetShop;

    /**
     * @var Project $translationProject
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @ORM\JoinColumn(name="translation_project_id")
     */
    protected $translationProject;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $price;

    /**
     * TranslationTask constructor.
     * @param string $taskId
     * @param Shop $sourceShop
     * @param Shop $targetShop
     * @param Project $translationProject
     * @param string $status
     * @param string $price
     */
    public function __construct(string $taskId, Shop $sourceShop, Shop $targetShop, Project $translationProject, string $status, string $price)
    {
        $this->taskId = $taskId;
        $this->sourceShop = $sourceShop;
        $this->targetShop = $targetShop;
        $this->translationProject = $translationProject;
        $this->status = $status;
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTaskId(): string
    {
        return $this->taskId;
    }

    /**
     * @param string $taskId
     */
    public function setTaskId(string $taskId): void
    {
        $this->taskId = $taskId;
    }

    /**
     * @return Shop
     */
    public function getSourceShop(): Shop
    {
        return $this->sourceShop;
    }

    /**
     * @param Shop $sourceShop
     */
    public function setSourceShop(Shop $sourceShop): void
    {
        $this->sourceShop = $sourceShop;
    }

    /**
     * @return Shop
     */
    public function getTargetShop(): Shop
    {
        return $this->targetShop;
    }

    /**
     * @param Shop $targetShop
     */
    public function setTargetShop(Shop $targetShop): void
    {
        $this->targetShop = $targetShop;
    }

    /**
     * @return Project
     */
    public function getTranslationProject(): Project
    {
        return $this->translationProject;
    }

    /**
     * @param Project $translationProject
     */
    public function setTranslationProject(Project $translationProject): void
    {
        $this->translationProject = $translationProject;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }
}
