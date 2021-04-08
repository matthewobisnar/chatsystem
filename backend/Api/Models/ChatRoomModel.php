<?php

namespace Api\Models;

use Core\Helpers\Database;
use Core\Helpers\Helper;

class ChatRoomModel
{
    public const TABLE_NAME = 'chat_room';
    
    public const TABLE_PREFIX = 'chat_room_';
    public const PRIMARY_COLUMN = 'chat_room_id';
    public const PRIMARY_CODE = 'chat_room_code';

    public const TABLE_COLUMNS = [
        'chat_room_id',
        'chat_room_code',
        'chat_room_name' ,
        'chat_room_status' ,
        'chat_room_created_at',
        'chat_room_created_by' ,
        'chat_room_deleted_at',
        'chat_room_deleted_by',
        'chat_room_updated_at',
        'chat_room_updated_by',
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