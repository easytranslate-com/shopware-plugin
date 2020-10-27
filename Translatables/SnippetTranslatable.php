<?php

namespace Easytranslate\Translatables;

use Doctrine\ORM\AbstractQuery;
use Easytranslate\Components\Easytranslate\Translatable;
use Easytranslate\Models\Task;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Snippet\Snippet;


class SnippetTranslatable implements Translatable
{
    protected $entityName;
    protected $objectType;
    protected $translationContent;
    protected $connection;
    /**
     * @var mixed|object|\Shopware\Components\DependencyInjection\Container|null
     */
    private $em;

    public function __construct()
    {
        $this->objectType = 'snippet';
        $this->entityName = 's_core_snippets';

        $this->container = Shopware()->Container();
        $this->connection = $this->container->get('dbal_connection');
        $this->em = $this->container->get('models');
        $this->translationComponent = $this->container->get('translation');
    }

    public function getTask($id): Task {
        /** @var QueryBuilder $builder */
        $builder = $this->em->getRepository(Task::class)->createQueryBuilder('task')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['taskId' => $id]
            );

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function getContent($identifier, $fieldsOfInterest, $sourceLanguage)
    {
        $sourceShopId = $sourceLanguage->getHostLanguage();

        /** @var Shop $shop */
        $shop = $this->em->find(Shop::class, $sourceShopId);
        $localeId = $shop->getLocale()->getId();

        $snippetNamespace = explode(':', $identifier)[0];
        $snippetName = explode(':', $identifier)[1];
        $this->translationContent = array(
            $identifier => $this->getSnippetValue($snippetNamespace, $snippetName, $sourceShopId, $localeId)
        );

        return $this->translationContent;
    }

    public function setContent($taskId, $identifier, $content, $fieldsOfInterest, $task)
    {
        $task = $this->getTask($taskId);

        $snippetNamespace = explode(':', $identifier)[0];
        $snippetName = explode(':', $identifier)[1];
        $shopId = $task->getTargetShop()->getId();
        $localeId = $task->getTargetShop()->getLocale()->getId();

        $repo = Shopware()->Models()->getRepository('Shopware\Models\Snippet\Snippet');
        /** @var Snippet $snippet */
        $snippet = $repo->findOneBy([
            'namespace' => $snippetNamespace,
            'name' => $snippetName,
            'shopId' => $shopId,
            'localeId' => $localeId]
        );

        if ($snippet) {
            // there is already a snippet --> overwrite
            $snippet->setValue($content[$identifier]);
        }
        else {
            // create new snippet
            $snippet = new Snippet();
            $snippet->setNamespace($snippetNamespace);
            $snippet->setName($snippetName);
            $snippet->setShopId($shopId);
            $snippet->setLocaleId($localeId);
            $snippet->setValue($content[$identifier]);
        }
        $this->em->persist($snippet);
        $this->em->flush();
    }

    private function getSnippetValue($namespace, $name, $shopId, $localeId)
    {
        if ($result = $this->getSnippet($namespace, $name, $shopId, $localeId)) {
            return $result;
        }

        $fallbackShopId = 1;
        if ($firstFallback = $this->getSnippet($namespace, $name, $fallbackShopId, $localeId)) {
            return $firstFallback;
        }

        $fallbackLocaleId = 1;
        return $this->getSnippet($namespace, $name, $fallbackShopId, $fallbackLocaleId);

    }

    private function getSnippet($namespace, $name, $shopId, $localeId) {
        $repo = Shopware()->Models()->getRepository('Shopware\Models\Snippet\Snippet');
        /** @var QueryBuilder $builder */
        $builder = $repo->createQueryBuilder('snippets');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter([
                'namespace' => $namespace,
                'name' => $name,
                'shopId' => $shopId,
                'localeId' => $localeId
            ]);

        return $builder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY)['value'];
    }

    public function getType()
    {
        return $this->objectType;
    }

    public function getId()
    {
        return null;
    }
}
