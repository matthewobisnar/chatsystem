<?php

namespace Api\Models;

use Core\Helpers\Database;
use Core\Helpers\Helper;

class MessageModel
{
    public const TABLE_NAME = 'message';
    
    public const TABLE_PREFIX = 'message_';
    public const PRIMARY_COLUMN = 'message_id';
    public const PRIMARY_CODE = 'message_code';

    public const TABLE_COLUMNS = [
        'message_id',
        'message_code',
        'message_chat_room_code',
        'message_content',
        'message_status',
        'message_created_at',
        'message_created_by',
        'message_deleted_at',
        'message_deleted_by',
        'message_updated_at',
        'message_updated_by'
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

    // Process
    public static function serverSentEvents()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        while (true) {
            
            // Every second, send a "ping" event.
            $curDate = date(DATE_ISO8601);
            echo 'data: This is a message at time ' . $curDate . "\n\n";
        
            ob_end_flush();
            flush();
            
            // Break the loop if the client aborted the connection (closed the page)
            if ( connection_aborted() ) break;
            
            sleep(1);
        }

    }
}