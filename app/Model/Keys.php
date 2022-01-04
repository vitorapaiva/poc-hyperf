<?php declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $uuid
 * @property $name
 * @property $key_type_id
 * @property $entity_id
 * @property $status_id
 * @property $value
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 */
class Keys extends Model
{
    protected $table = 'keys';
}
