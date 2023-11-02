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
        $ret = ArticleModel::_()->getList(1, 10);
        return $ret;
    }
    public function getArticleList($page = 1, $page_size = 10)
    {
        $ret = ArticleModel::_()->getList($page, $page_size);
        return $ret;
    }
    public function getArticle($id, $comment_pge = 1, $page_size = 10)
    {
        $ret = ArticleModel::_()->get($id);
        if (!$ret) {
            return array();
        }
        $ret['comments'] = CommentModelEx::_()->getListByArticle($id, $comment_pge);
        
        return $ret;
    }
    public function getArticleFullInfo($id, $page = 1, $page_size = 10)
    {
        $art = ArticleModel::_()->get($id);
        list($comments, $total) = CommentModelEx::_()->getListByArticle($id, $page, $page_size);
        $art['comments'] = $comments;
        $art['comments_total'] = $total;
        return $art;
    }
}
