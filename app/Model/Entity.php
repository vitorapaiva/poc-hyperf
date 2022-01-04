<?php declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $entity_identifier
 * @property $entity_type
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 */
class Entity extends Model
{
    protected $table = 'entities';
}
