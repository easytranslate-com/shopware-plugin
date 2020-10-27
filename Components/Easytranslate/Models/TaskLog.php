<?php

namespace Easytranslate\Components\Easytranslate\Models;

use DateTime;

/**
 * Class TaskLog
 * @package Easytranslate\Components\Easytranslate\Models
 */
class TaskLog
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var
     */
    protected $taskId;

    /**
     * @var Task $task
     */
    protected $task;

    /**
     * @var string $content
     */
    protected $content;

    /**
     * @var DateTime $timestamp
     */
    protected $timestamp;

    /**
     * TranslationTaskLog constructor.
     * @param Task $task
     * @param string $content
     */
    public function __construct(Task $task, string $content)
    {
        $this->task = $task;
        $this->content = $content;
        $this->timestamp = new DateTime('now');
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
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     */
    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp(DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
