<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Model;


class CommentModel extends Base
{
    public $table_name = "Comments";
    public function getListByArticle($article_id, int $page = 1, int $page_size = 10)
    {
        $start = $page - 1;
        $sql = "SELECT  * from `'TABLE'` where article_id=? and deleted_at is null order by id desc limit $start,$page_size";
        $data = $this->fetchAll($sql, $article_id);
        $sql = "SELECT count(*) as c from `'TABLE'`";
        $total = $this->fetchColumn($sql);
        return [$total, $data];
    }
    public function addData($user_id, $article_id, $content)
    {
        $data = array('user_id' => $user_id,'article_id' => $article_id,'content' => $content, 'created_at'=>date('Y-m-d H:i:s'));
        return parent::add($data);
    }
    public function delete($id)
    {
        return parent::delete($id);
    }
    public function getList($where=[] ,$page =1 ,$page_size =10)
    {
        $where['deleted_at'] = $where['deleted_at']??false;
        return parent::getList($where, $page, $page_size);
    }
}
