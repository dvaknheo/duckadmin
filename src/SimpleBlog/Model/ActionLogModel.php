<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Model;

class ActionLogModel extends Base
{
    protected $table_name = 'ActionLogs';
    
    public function log($action, $type = '')
    {
        $this->insertData($this->table(), ['contents' => $action,'type' => $type,'created_at' => date('Y-m-d H:i:s')]);
    }
    public function get($id)
    {
    }
    public function getList(int $page = 1, int $page_size = 10)
    {
        $start = $page - 1;
        $sql = "SELECT SQL_CALC_FOUND_ROWS  * from 'TABLE' where true order by id desc limit $start,$page_size";
        $sql = $this->prepare($sql);
        $data = $this->fetchAll($sql);
        $sql = "SELECT FOUND_ROWS()";
        $total = $this->fetchColumn($sql);
        return array($data,$total);
    }
}
