<?php

namespace Easytranslate\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Easytranslate\Components\Easytranslate\Models\Task;
use Shopware\Components\Model\ModelEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_translation_project")
 */
class Project extends ModelEntity
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
     * @ORM\Column(name="project_id", type="string", nullable=true, unique=true)
     */
    private $projectId = null;

    /**
     * @var string
     *
     * @ORM\Column(name="project_name", type="string", nullable=true)
     */
    private $projectName = null;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="object_type", type="string", nullable=false)
     */
    private $objectType;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="fields_of_interest", type="string", nullable=false)
     */
    private $fieldsOfInterest;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="callback_token", type="string", nullable=false)
     */
    private $callbackToken;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="price", type="string", nullable=false)
     */
    private $price;

    /**
     * @var Task[]
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="translationProject")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $tasks;

    /**
     * TranslationProject constructor.
     * @param string $projectId
     * @param string $projectName
     * @param string $objectType
     * @param string $fieldsOfInterest
     * @param string $callbackToken
     * @param string $status
     * @param string $price
     */
    public function __construct(string $projectId, string $projectName, string $objectType, string $fieldsOfInterest, string $callbackToken, string $status = 'CREATED', string $price = '')
    {
        $this->projectId = $projectId;
        $this->projectName = $projectName;
        $this->objectType = $objectType;
        $this->fieldsOfInterest = $fieldsOfInterest;
        $this->callbackToken = $callbackToken;
        $this->status = $status;
        $this->price = $price;
        $this->tasks = new ArrayCollection();
    }

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
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     */
    public function setProjectId(string $projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     */
    public function setProjectName(string $projectName): void
    {
        $this->projectName = $projectName;
    }

    /**
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     */
    public function setObjectType(string $objectType): void
    {
        $this->objectType = $objectType;
    }

    /**
     * @return string
     */
    public function getFieldsOfInterest(): string
    {
        return $this->fieldsOfInterest;
    }

    /**
     * @param string $fieldsOfInterest
     */
    public function setFieldsOfInterest(string $fieldsOfInterest): void
    {
        $this->fieldsOfInterest = $fieldsOfInterest;
    }

    /**
     * @return string
     */
    public function getCallbackToken(): string
    {
        return $this->callbackToken;
    }

    /**
     * @param string $callbackToken
     */
    public function setCallbackToken(string $callbackToken): void
    {
        $this->callbackToken = $callbackToken;
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

    /**
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
