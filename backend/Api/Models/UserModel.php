<?php
namespace Api\Models;

use Core\Helpers\Database;
use Core\Helpers\Helper;

class UserModel
{
    public const TABLE_NAME = 'user';
    
    public const TABLE_PREFIX = 'user_';
    public const PRIMARY_COLUMN = 'user_id';
    public const PRIMARY_CODE = 'user_code';

    public const TABLE_COLUMNS = [
        'user_id',
        'user_code',
        'user_name',
        'user_status',
        'user_created_at',
        'user_created_by',
        'user_deleted_at',
        'user_deleted_by',
        'user_updated_at',
        'user_updated_by'
    ];

    public static function Create()
    {
        return Helper::response(Helper::RETURN, false, 
            Database::genericCreate(
                self::TABLE_NAME, 
                self::TABLE_COLUMNS,
                self::TABLE_PREFIX,
                self::PRIMARY_CODE, 
                self::PRIMARY_COLUMN
            )
        );
    }

    public static function Update()
    {
        return Helper::response(Helper::RETURN, true, 
            Database::genericUpdate(
                self::TABLE_NAME, 
                self::TABLE_COLUMNS,
                self::TABLE_PREFIX,
                self::PRIMARY_CODE, 
                self::PRIMARY_COLUMN
            )
        );
    }

    public static function select()
    {
        return Database::genericSelect(
            self::TABLE_NAME, 
            self::TABLE_COLUMNS,
            self::TABLE_PREFIX,
            self::PRIMARY_CODE, 
            self::PRIMARY_COLUMN
        );
    }
}