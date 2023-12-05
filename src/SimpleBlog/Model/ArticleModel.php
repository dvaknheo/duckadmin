<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Model;

class ArticleModel extends Base
{
    public $table_name = "Articles";
    public function addData($title, $content)
    {
        $data = array('title' => $title,'content' => $content);
        $date = date('Y-m-d H:i:s');
        $data['created_at'] = $date;
        $data['updated_at'] = $date;
        
        return parent::add($data);
    }
    public function get($id)
    {
        return parent::get($id);
    }
    public function getList($where=[] ,$page =1 ,$page_size =10)
    {
        return parent::getList($where, $page, $page_size);
    }
    public function updateData($id, $title, $content)
    {
        $data = array('title' => $title,'content' => $content);
        $date = date('Y-m-d H:i:s');
        $data['updated_at'] = $date;
        return parent::update($id, $data);
    }
    public function delete($id)
    {
        return parent::delete($id);
    }
}
