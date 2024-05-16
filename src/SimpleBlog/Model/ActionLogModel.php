<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Model;

class ActionLogModel extends Base
{
    protected $table_name = 'ActionLogs';
    
    public function getList($where = [], int $page = 1, int $page_size = 10)
    {
        return parent::getList($where, $page, int $page_size);
    }
    public function log($action, $type = '')
    {
        $this->add(['contents' => $action,'type' => $type,'created_at' => date('Y-m-d H:i:s')]);
    }
}
