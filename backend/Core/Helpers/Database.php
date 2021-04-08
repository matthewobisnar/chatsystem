<?php
namespace Core\Helpers;

class Database
{
    public static $COLUMNS = [];

    public static function processQuery ($query, array $args = array())
    {
        $output = [];

        try {
            $pdo = new \PDO (
                'mysql:host=localhost;port=3306;dbname=chat_db',
                'chat_admin',
                'root',
                array (
                    \PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"
                )
            );

            $pdo_statement = $pdo->prepare($query);

            foreach ($args as $index => $arg) {
                if (is_int($arg)) {
                    $type = \PDO::PARAM_INT;
                    $arg = filter_var(trim(urlencode($arg)), FILTER_SANITIZE_NUMBER_INT);
                } elseif (is_bool($arg)) {
                    $type = \PDO::PARAM_BOOL;
                    $arg = filter_var(trim(urlencode($arg)), FILTER_SANITIZE_NUMBER_INT);
                } elseif (is_null($arg)) {
                    $type = \PDO::PARAM_NULL;
                    $arg = NULL;
                } else {
                    $type = \PDO::PARAM_STR;
                    // $arg = filter_var(trim(urldecode($arg)), FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
                }
    
                try {
                    $pdo_statement->bindValue($index + 1, $arg, $type);
                } catch (\Exception $e) {
                    $pdo = NULL;
                   new \Exception($e->getMessage());
                }
            }

            try {
                $pdo_statement->execute();
    
                if ($pdo_statement->rowCount()) {
                    $output = (preg_match('/\b(update|insert|delete)\b/', strtolower($query)) === 1) ? ["status" => true, "last_inserted_id" => !is_null($pdo) ? $pdo->lastInsertId() : null] : $pdo_statement->fetchAll(\PDO::FETCH_ASSOC);
                }
            } catch (\PDOException $e) {
                $pdo = NULL;
                return new \PDOException($e->getMessage());
            }
    
            $pdo = NULL;
            flush();
            return empty($output) ? [] : $output;
            
        } catch (\PDOException $e) {
            $pdo = NULL;
            return new \PDOException($e->getMessage());
        }
    }

    public static function genericCreate($table, $columns, $table_prefix, $primary_code, $primary_id)
    {
        $request = Helper::postRequest() ?? self::$COLUMNS;
        $insert_columns = [];

        // Check if the payload key is exists in table column.
        // return error response if request table does not exists.
        foreach (array_keys($request) as $column) {
            if (!in_array($column, $columns)) {
                Helper::response(Helper::DIE, true, true, [
                    $column . " does column not exists."
                ]);
            }
        }

        // Insert only available columns.
        foreach ($columns as $column) {
            if (in_array($column , array_keys($request))) {
                $insert_columns[$column] = $request[$column];
            }

            if (isset($insert_columns[$primary_code]) || isset($insert_columns[$primary_id])) {
                unset($insert_columns[$primary_code]);
                unset($insert_columns[$primary_id]);
            }
        }

        $insert_columns[$primary_code] = Helper::Randomizer(16, Helper::ALPHA_NUMERIC);
        $insert_columns[$table_prefix.'created_at'] = date("Y-m-d H:i:s");
        
        if(!isset($insert_columns[$table_prefix.'created_by'])) {
            $insert_columns[$table_prefix.'created_by'] = Helper::CREATED_BY;
        }

        $column = trim(implode(", ", array_keys($insert_columns)), ',');
        $bindValue = trim(str_repeat('? , ', count($insert_columns)), ' ,');

        $response = self::processQuery("INSERT INTO {$table} ({$column}) VALUES ({$bindValue})", array_values($insert_columns));

        return $response;
    }

    public static function genericUpdate($table, $columns, $table_prefix, $primary_code, $primary_id)
    {
        
        $request = Helper::postRequest() ?? self::$COLUMNS;
        $insert_columns = [];
        $where =[];

        // Check if the payload key is exists in table column.
        // return error response if request table does not exists.
        foreach (array_keys($request) as $column) {
            if (!in_array($column, $columns)) {
                Helper::response(Helper::DIE, true, true, [
                    $column . " does column not exists."
                ]);
            }
        }

        if (in_array($primary_code, array_keys($request))) {

            $where = $primary_code . "=?";
            $whereValue = $request[$primary_code];
            unset($request[$primary_code]);

        } else {
            Helper::response(Helper::DIE, false, true, [
                $primary_code . " column is required for update."
            ]);
        }

        // Insert only available columns.
        foreach ($columns as $column) {
            if (in_array($column , array_keys($request))) {
                $insert_columns[$column] = $request[$column];
            }
        }

        $insert_columns[$table_prefix.'updated_at'] = date("Y-m-d H:i:s");
        $insert_columns[$table_prefix.'updated_by'] = Helper::CREATED_BY;

        $process = self::processQuery("SELECT `user_id` FROM {$table} LIMIT 1", [$insert_columns[$table_prefix.'user_code']]);

        if (!empty($process)) {
            $column = trim(sprintf(str_repeat('%s =? ,', count($insert_columns)),  ...array_keys($insert_columns)), ',');
            $response = self::processQuery("UPDATE {$table} SET {$column} WHERE ({$where})", array_merge(array_values($insert_columns), [$whereValue]));
    
            return $response;   
        } else {
            Helper::response(Helper::DIE, false, true, [
                $insert_columns[$table_prefix.'user_code'] . " does not exists."
            ]);
        }
    }

    public static function genericSelect($table, $columns, $table_prefix, $primary_code, $primary_id)
    {
        $request = empty(Helper::postRequest()) ? (empty(Helper::getRequest()) ? self::$COLUMNS : Helper::getRequest()) : Helper::postRequest();
        $fields = Helper::getArrayValue($request, 'fields');
        $limit = !empty(Helper::getArrayValue($request, 'limit')) ? (int) Helper::getArrayValue($request, 'limit') : null;
        $offset = !empty(Helper::getArrayValue($request, 'offset')) ? (int) Helper::getArrayValue($request, 'offset') : null;
        $where = !empty(Helper::getArrayValue($request, 'where')) ? Helper::getArrayValue($request, 'where'): null;
        $leftJoin = !empty(Helper::getArrayValue($request, 'join')) ? Helper::getArrayValue($request, 'join'): null;

        $limit_query =null;
        $where_query = null;
        $query_values = [];
        $joinsValue = null;

        // return error response if request table does not exists.

        if (empty($leftJoin)) {

            foreach ($fields as $column) {
                if (!in_array($column, $columns)) {
                    Helper::response(Helper::DIE, true, true, [
                        $column . " does column not exists."
                    ]);
                }
            }

        } else {
            foreach ($leftJoin as $joins) {
               $joinsValue .= " LEFT JOIN " . $joins['tableWith'] . " ON " . $joins['columnWith'] . " = " . $joins['columnBy'];
            }
        }

        if (!empty($where)) {

            $where_query = null;

            foreach ($where as $keyWhere => $valWhere) {
                $where_query .= " WHERE " . $keyWhere . " = ?, AND " ;
                $query_values[] = $valWhere;
            }

            $where_query = trim($where_query, ", AND");
        }

        if (isset($limit) && !empty($limit)) {      
            $limit_query =  ' LIMIT ' . (!empty(($offset)) ? ' ? , ' : null) . '?';
            $query_values[] = $offset;
            $query_values[] = $limit;
        }

        if (empty($request)) {
            $output = self::processQuery("SELECT * FROM {$table}", []);
        } else {

            if (isset($fields) && !empty($fields)) {
                $column = trim(implode(' ,', array_values($fields)), ',');
            } else {
                $column = "*";
            }

            $output = self::processQuery("SELECT {$column} FROM {$table} {$joinsValue} {$where_query} {$limit_query}" , array_merge(array_filter($query_values)));
        }

        return [
            "count" => self::processQuery("SELECT count(*) as total FROM {$table}", []),
            'data' => $output
        ];
    }
}