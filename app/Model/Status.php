<?php declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $slug
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 */
class Status extends Model
{
    protected $table = 'status';
}
