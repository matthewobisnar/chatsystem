<?php
namespace Api\Models;

use Core\Helpers\Database;

class MigrationModel
{
    public function __construct()
    {
        $this->createUserTable();
        $this->createChatRoom();
        $this->createMessageTable();
    }

    public function createUserTable()
    {
       return (new Database())->processQuery(
            "CREATE TABLE `user` (
                `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
                `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `user_status` tinyint(1) NOT NULL DEFAULT '1',
                `user_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `user_created_by` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
                `user_deleted_at` timestamp NULL DEFAULT NULL,
                `user_deleted_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
                `user_updated_at` timestamp NULL DEFAULT NULL,
                `user_updated_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`user_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        , []);
    }

    public function createChatRoom()
    {
        return (new Database())->processQuery(
            "CREATE TABLE `chat_room` (
                `chat_room_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `chat_room_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
                `chat_room_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `chat_room_status` tinyint(1) NOT NULL DEFAULT '1',
                `chat_room_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `chat_room_created_by` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
                `chat_room_deleted_at` timestamp NULL DEFAULT NULL,
                `chat_room_deleted_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
                `chat_room_updated_at` timestamp NULL DEFAULT NULL,
                `chat_room_updated_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`chat_room_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        , []);
    }

    public function createMessageTable()
    {
        return (new Database())->processQuery(
            "CREATE TABLE `message` (
            `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `message_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
            `message_chat_room_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
            `message_content` text COLLATE utf8_unicode_ci NOT NULL,
            `message_status` tinyint(1) NOT NULL DEFAULT '1',
            `message_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `message_created_by` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
            `message_deleted_at` timestamp NULL DEFAULT NULL,
            `message_deleted_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
            `message_updated_at` timestamp NULL DEFAULT NULL,
            `message_updated_by` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
            PRIMARY KEY (`message_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
        []);
    }
}