<?php
namespace Api\Controllers;

use Core\Helpers\Helper;
use Api\Models\MigrationModel;

class UtilsController
{
    public function actionMigrate()
    {
        Helper::response(Helper::DIE, false, new MigrationModel());
    }
}