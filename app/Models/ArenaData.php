<?php

namespace App\Models;

class ArenaData extends Model
{
    public function rows($sql, $types = '', array $params = array())
    {
        $statement = $this->statement($sql, $types, $params);

        if (!$statement) {
            return array();
        }

        $result = mysqli_stmt_get_result($statement);
        $rows = array();

        while ($result && $row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        mysqli_stmt_close($statement);

        return $rows;
    }

    public function row($sql, $types = '', array $params = array())
    {
        $rows = $this->rows($sql, $types, $params);

        return isset($rows[0]) ? $rows[0] : null;
    }

    public function value($sql, $types = '', array $params = array(), $column = 'value')
    {
        $row = $this->row($sql, $types, $params);

        return $row && array_key_exists($column, $row) ? $row[$column] : null;
    }

    public function execute($sql, $types = '', array $params = array())
    {
        $statement = $this->statement($sql, $types, $params);

        if (!$statement) {
            return false;
        }

        $success = mysqli_stmt_errno($statement) === 0;
        mysqli_stmt_close($statement);

        return $success;
    }

    public function connection()
    {
        return $this->db();
    }

    protected function statement($sql, $types, array $params)
    {
        $statement = mysqli_prepare($this->db(), $sql);

        if (!$statement) {
            return null;
        }

        if ($types !== '') {
            $values = array_values($params);
            $bind = array($statement, $types);

            foreach ($values as $index => $value) {
                $bind[] = &$values[$index];
            }

            if (!call_user_func_array('mysqli_stmt_bind_param', $bind)) {
                mysqli_stmt_close($statement);
                return null;
            }
        }

        if (!mysqli_stmt_execute($statement)) {
            mysqli_stmt_close($statement);
            return null;
        }

        return $statement;
    }
}
