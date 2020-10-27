<?php

namespace Easytranslate\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_translation_task_log")
 */
class TaskLog extends ModelEntity
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
     * @var
     * @ORM\Column(name="translation_task_id", type="integer")
     */
    protected $translationTaskId;

    /**
     * @var Task $translationTask
     *
     * @ORM\ManyToOne(targetEntity="Task", cascade={"persist"})
     * @ORM\JoinColumn(name="translation_task_id", referencedColumnName="id", nullable=true)
     */
    protected $translationTask;

    /**
     * @var string $content
     *
     * @ORM\Column(name="content", type="string", nullable=true)
     */
    protected $content;

    /**
     * @var DateTime $timestamp
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * TranslationTaskLog constructor.
     * @param Task $translationTask
     * @param string $content
     */
    public function __construct(Task $translationTask, string $content)
    {
        $this->translationTask = $translationTask;
        $this->content = $content;
        $this->timestamp = new DateTime('now');
    }

    /**
     * @param $entity TaskLog
     */
    public static function from($entity): TaskLog {

    }

    /**
     * @return Task
     */
    public function getTranslationTask(): Task
    {
        return $this->translationTask;
    }

    /**
     * @param Task $translationTask
     */
    public function setTranslationTask(Task $translationTask): void
    {
        $this->translationTask = $translationTask;
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
