<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Business;

use SimpleBlog\Model\ArticleModel;
use SimpleBlog\Model\CommentModelEx;

class ArticleBusiness extends Base
{
    public function getRecentArticle()
    {
        $ret = ArticleModel::_()->getList([], 1, 10);
        $ret = [$ret['data'],$ret['count']];
        return $ret;
    }
    public function getArticleList($page = 1, $page_size = 10)
    {
        $ret = ArticleModel::_()->getList([], $page, $page_size);
        
        $ret = [$ret['data'],$ret['count']];
        return $ret;
    }
    public function getArticle($id, $comment_pge = 1, $page_size = 10)
    {
        $ret = ArticleModel::_()->get($id);
        if (!$ret) {
            return array();
        }
        $ret['comments'] = CommentModelEx::_()->getListByArticle([], $id, $comment_pge);
        //我们这里改成整合 ID 重新来
        return $ret;
    }
    public function getArticleFullInfo($id, $page = 1, $page_size = 10)
    {
        $art = ArticleModel::_()->get($id);
        $data = CommentModelEx::_()->getListByArticle([],$id, $page, $page_size);
        //我们这里改成整合 ID 重新来
        $art['comments'] = $data['data'];
        $art['comments_total'] = $data['count'];
        return $art;
    }
}
