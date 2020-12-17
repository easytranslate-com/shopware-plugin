<?php

namespace Easytranslate\Components\Easytranslate\Models;

use DateTime;
use Easytranslate\Components\Easytranslate\Language;

/**
 * Class Task
 * @package Easytranslate\Components\Easytranslate\Models
 */
class Task
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $taskId;

    /**
     * @var Language $sourceLocale
     */
    protected $sourceLocale;

    /**
     * @var Language $targetLocale
     */
    protected $targetLocale;

    /**
     * @var Project $project
     */
    protected $project;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var string $price
     */
    protected $price;

    /**
     * @var DateTime|null
     */
    private $creationDate = null;

    /**
     * @var DateTime|null
     */
    private $deadline = null;


    /**
     * Task constructor.
     * @param string $taskId
     * @param Language $sourceLocale
     * @param Language $targetLocale
     * @param Project $translationProject
     * @param string $status
     * @param string $price
     * @param DateTime|null $creationDate
     * @param DateTime|null $deadline
     */
    public function __construct(string $taskId, Language $sourceLocale, Language $targetLocale,
                                Project $translationProject, string $status, string $price, ?\DateTime $creationDate,
                                ?\DateTime $deadline)
    {
        $this->taskId = $taskId;
        $this->sourceLocale = $sourceLocale;
        $this->targetLocale = $targetLocale;
        $this->project = $translationProject;
        $this->status = $status;
        $this->price = $price;
        $this->creationDate = $creationDate;
        $this->deadline = $deadline;
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
     * @return string
     */
    public function getSourceLocale(): Language
    {
        return $this->sourceLocale;
    }

    /**
     * @param string $sourceLocale
     */
    public function setSourceLocale(Language $sourceLocale): void
    {
        $this->sourceLocale = $sourceLocale;
    }

    /**
     * @return Language
     */
    public function getTargetLocale(): Language
    {
        return $this->targetLocale;
    }

    /**
     * @param Language $targetLocale
     */
    public function setTargetLocale(Language $targetLocale): void
    {
        $this->targetLocale = $targetLocale;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
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
     * @return DateTime|null
     */
    public function getCreationDate(): ?DateTime
    {
        return $this->creationDate;
    }

    /**
     * @param DateTime|null $creationDate
     */
    public function setCreationDate(?DateTime $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return DateTime|null
     */
    public function getDeadline(): ?DateTime
    {
        return $this->deadline;
    }

    /**
     * @param DateTime|null $deadline
     */
    public function setDeadline(?DateTime $deadline): void
    {
        $this->deadline = $deadline;
    }


}
