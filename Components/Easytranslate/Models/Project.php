<?php

namespace Easytranslate\Components\Easytranslate\Models;

/**
 * Class Project
 * @package Easytranslate\Components\Easytranslate\Models
 */
class Project
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $projectName;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var string
     */
    private $fieldsOfInterest;

    /**
     * @var string
     */
    private $callbackToken;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $price;

    /**
     * @var array
     */
    private $tasks;


    /**
     * Project constructor.
     * @param string $projectId
     * @param string $projectName
     * @param string $objectType
     * @param string $fieldsOfInterest
     * @param string $callbackToken
     * @param string $status
     * @param string $price
     */
    public function __construct(string $projectId, string $projectName, string $objectType, string $fieldsOfInterest,
                                string $callbackToken, string $status = 'CREATED', string $price = '{}')
    {
        $this->projectId = $projectId;
        $this->projectName = $projectName;
        $this->objectType = $objectType;
        $this->fieldsOfInterest = $fieldsOfInterest;
        $this->callbackToken = $callbackToken;
        $this->status = $status;
        $this->price = $price;
        $this->tasks = [];
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
     * @param Task $task
     */
    public function addTask(Task $task) {
        $this->tasks[] = $task;
    }

    /**
     * @param array $tasks
     */
    public function setTasks(array $tasks) {
        $this->tasks = $tasks;
    }

    /**
     * @return array
     */
    public function getTasks(): array {
        return $this->tasks;
    }
}
