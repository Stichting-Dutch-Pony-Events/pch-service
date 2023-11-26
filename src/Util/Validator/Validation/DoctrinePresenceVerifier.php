<?php

namespace App\Util\Validator\Validation;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Str;
use Illuminate\Validation\PresenceVerifierInterface;

class DoctrinePresenceVerifier implements PresenceVerifierInterface
{
    private ArrayCollection $cachedQueries;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->cachedQueries = new ArrayCollection();
    }

    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $query = $this->entityManager->getConnection()->createQueryBuilder()
            ->from($collection)
            ->where("$column = :primaryColumn")
            ->setParameter('primaryColumn', $value);

        // Laravel also has 'NULL' check, added to be sure
        if ($excludeId !== null && $excludeId !== 'NULL') {
            $this->addWhere($query, $idColumn ?: 'id', "!$excludeId");
        }

        $this->addConditions($query, $extra);

        return $this->getResult($query);
    }

    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $query = $this->entityManager->getConnection()->createQueryBuilder()
            ->from($collection)
            ->where("$column IN(:primaryColumn)")
            ->setParameter('primaryColumn', $values, Types::ARRAY);

        $this->addConditions($query, $extra)->distinct();

        return $this->getResult($query);
    }

    protected function addConditions(QueryBuilder $query, $conditions): QueryBuilder
    {
        foreach ($conditions as $key => $value) {
            if ($value instanceof Closure) {
                $value($query);
            } else {
                $this->addWhere($query, $key, $value);
            }
        }

        return $query;
    }

    protected function addWhere(QueryBuilder $query, string $key, string $extraValue): void
    {
        $letters   = array_merge(range('A', 'Z'), range('a', 'z'));
        $parameter = $letters[array_rand($letters)] . Str::random('6');

        if ($extraValue === 'NULL') {
            $query->andWhere("$key IS NULL");
        } elseif ($extraValue === 'NOT_NULL') {
            $query->andWhere("$key IS NOT NULL");
        } elseif (Str::startsWith($extraValue, '!')) {
            $extraValue = mb_substr($extraValue, 1);
            $query->andWhere("$key != :$parameter")
                ->setParameter($parameter, $extraValue);
        } else {
            $query->andWhere("$key = :$parameter")
                ->setParameter($parameter, $extraValue);
        }
    }

    private function getResult($query): bool|int {
        $cachedQuery = $this->cachedQueries->findFirst(function ($key, CachedQuery $cache) use ($query) {
            return $cache->query === (string)$query->select('COUNT(*)') && $cache->parameters === $query->getParameters();
        });

        if ($cachedQuery instanceof CachedQuery) {
            return $cachedQuery->result;
        }

        $result = $this->entityManager->getConnection()->fetchOne(
            (string)$query->select('COUNT(*)'),
            $query->getParameters(),
            $query->getParameterTypes()
        );

        $this->cachedQueries->add(new CachedQuery(
            (string) $query->select('COUNT(*)'),
            $query->getParameters(),
            $result
        ));

        return $result;
    }
}
