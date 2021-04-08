<?php
namespace Api\Controllers;

use Api\Models\ChatRoomModel;
use Core\Helpers\Helper;
use Api\Models\UserModel;
use Api\Models\MessageModel;

class ChatController
{
    // ===================================================
    // User Controller
    
    public function actionCreateUser()
    {
        Helper::response(Helper::DIE, true, false, UserModel::Create());
    }

    public function actionUpdateUser()
    {
        Helper::response(Helper::DIE, true, false, UserModel::update());
    }

    public function actionSelectUsers()
    {
        Helper::response(Helper::DIE, true, false, UserModel::select());
    }

    // ===================================================
    // Chat Controller
    
    public function actionCreateChatRoom()
    {
        Helper::response(Helper::DIE, true, false, ChatRoomModel::Create());
    }

    public function actionSelectChatRooms()
    {
        Helper::response(Helper::DIE, true, false, ChatRoomModel::select());
    }

    // ==================================================
    // Message Controller

    public function actionGetMessages()
    {
        Helper::response(Helper::DIE, true, false, MessageModel::select());
    }

    public function actionCreateMessage()
    {
        Helper::response(Helper::DIE, true, false, MessageModel::create());
    }

    // ===================================================
    // Process Controller

    public function actionServerSentEvents()
    {
        return MessageModel::serverSentEvents();
    }

}