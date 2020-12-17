<?php

namespace Easytranslate\Setup;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;

class Updater
{
    /**
     * @var CrudService
     */
    private $attributeCrudService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(CrudService $attributeCrudService, ModelManager $modelManager, Connection $connection)
    {
        $this->attributeCrudService = $attributeCrudService;
        $this->modelManager = $modelManager;
        $this->connection = $connection;
    }

    /**
     * @param string $oldVersion
     */
    public function update($oldVersion)
    {
        if (\version_compare($oldVersion, '1.0.3', '<')) {
            $this->updateTo103();
        }
    }

    private function updateTo103()
    {
        if (!$this->checkIfColumnExist('s_plugin_translation_task', 'creation_date')) {
            $sql = 'ALTER TABLE `s_plugin_translation_task`
                    ADD COLUMN `creation_date` DATETIME';
            $this->connection->executeQuery($sql);
        }

        if (!$this->checkIfColumnExist('s_plugin_translation_task', 'deadline')) {
            $sql = 'ALTER TABLE `s_plugin_translation_task`
                    ADD COLUMN `deadline` DATETIME';
            $this->connection->executeQuery($sql);
        }
    }

    /**
     * Helper function to check if a column exists which is needed during update
     *
     * @param string $tableName
     * @param string $columnName
     *
     * @return bool
     */
    private function checkIfColumnExist($tableName, $columnName)
    {
        $sql = <<<SQL
SELECT column_name
FROM information_schema.columns
WHERE table_name = :tableName
    AND column_name = :columnName
    AND table_schema = DATABASE();
SQL;

        $columnNameInDb = $this->connection->executeQuery(
            $sql,
            ['tableName' => $tableName, 'columnName' => $columnName]
        )->fetchColumn();

        return $columnNameInDb === $columnName;
    }
}
