<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Business;

use SimpleBlog\Model\ActionLogModel;
use SimpleBlog\Model\CommentModel;
use SimpleBlog\Model\UserModel;

class UserBusiness extends Base
{
    public function addComment($user_id, $article_id, $content)
    {
        $username = Helper::_()->getUsername($user_id);
        
        CommentModel::_()->addData($user_id, $article_id, $content);
        ActionLogModel::_()->log("{$user_id}-{$username} 评论成功");
    }
    public function deleteCommentByUser($user_id, $comment_id)
    {
        $username = Helper::_()->getUsername($user_id);
        
        $comment = CommentModel::_()->get($comment_id);
        Helper::BusinessThrowOn(!$comment, "没找到评论",-1);
        Helper::BusinessThrowOn($comment['user_id'] != $user_id, "不是你的评论", -1);
        CommentModel::_()->delete($id);
        ActionLogModel::_()->log("{$user_id}-{$username} 删除评论成功");
    }
}
