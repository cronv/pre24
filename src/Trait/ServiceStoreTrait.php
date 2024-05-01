<?php

namespace cronv\Task\Management\Trait;


use cronv\Task\Management\Exception\StorageException;
use Throwable;

/**
 * Trait for storing multiple entities in persistent storage
 */
trait ServiceStoreTrait
{
    /**
     * Store an entity in the persistent storage.
     *
     * @param array|object ...$entities The entity to store.
     *
     * @throws StorageException If an error occurs while saving the entity to the persistent storage.
     */
    public function store(...$entities): void
    {
        $doctrine = $this->em;

        try {
            $manager = $doctrine->getManager();
            foreach ($entities as $entity) {
                if (is_array($entity)) {
                    foreach ($entity as $subEntity) {
                        $manager->persist($subEntity);
                    }
                    continue;
                }
                $manager->persist($entity);
            }
            $manager->flush();
        } catch (Throwable $e) {
            throw new StorageException('Error while save to persistent storage: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete an entity from the persistent storage.
     *
     * @param array|object ...$entities The entity to delete.
     *
     * @throws StorageException If an error occurs while deleting the entity from the persistent storage.
     */
    public function deleteMultipleRecords(...$entities): void
    {
        if (!$entities) {
            return;
        }

        $doctrine = $this->em;
        try {
            $manager = $doctrine->getManager();
            foreach ($entities as $entity) {
                if (is_array($entity)) {
                    foreach ($entity as $subEntity) {
                        $manager->remove($subEntity);
                    }
                    continue;
                }
                $manager->remove($entity);
            }
            $manager->flush();
        } catch (Throwable $e) {
            throw new StorageException('Error while save to persistent storage: ' . $e->getMessage(), 0, $e);
        }
    }
}
