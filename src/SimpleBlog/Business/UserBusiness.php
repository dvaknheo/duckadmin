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
    public function reg($username, $password)
    {
        $user = UserModel::_()->getUserByName($username);
        UserException::ThrowOn($user, "用户已经存在");
        $id = UserModel::_()->addUser($username, $password);
        UserException::ThrowOn(!$id, "注册失败");
        
        ActionLogModel::_()->log("$username 注册", 'reg');
        
        return UserModel::_()->getUserDirect($id);
    }
    public function login($username, $password)
    {
        $user = UserModel::_()->getUserByName($username);
        UserException::ThrowOn(!$user, "用户不存在");
        
        $flag = UserModel::_()->checkPass($password, $user['password']);
        UserException::ThrowOn(!$flag, "密码错误");
        
        ActionLogModel::_()->log("$username 登录成功");
        unset($user['password']);
        ActionLogModel::_()->log("{$user['username']} 登录");
        return $user;
    }
    public function getUser($id)
    {
        $user = UserModel::_()->getUserDirect($id);
        unset($user['password']);
        return $user;
    }
    // 以下是各种操作
    
    public function changePassword($user_id, $oldpass, $newpass)
    {
        $user = UserModel::_()->getUserDirect($user_id);
        UserException::ThrowOn(!$user, "用户不存在");
        
        $flag = UserModel::_()->checkPass($oldpass, $user['password']);
        UserException::ThrowOn(!$flag, "旧密码错误");
        
        UserModel::_()->changePass($user['id'], $newpass);
        ;
        ActionLogModel::_()->log("{$user['username']} 修改了登录密码");
    }
    public function addComment($user_id, $article_id, $content)
    {
        $user = UserModel::_()->getUserDirect($user_id);
        UserException::ThrowOn(!$user, "用户不存在");
        
        CommentModel::_()->addData($user_id, $article_id, $content);
        ActionLogModel::_()->log("{$user['username']} 评论成功");
    }
    public function deleteCommentByUser($user_id, $comment_id)
    {
        $user = UserModel::_()->getUserDirect($user_id);
        UserException::ThrowOn(!$user, "用户不存在");
        
        $comment = CommentModel::_()->get($comment_id);
        UserException::ThrowOn(!$comment, "没找到评论");
        UserException::ThrowOn($comment['user_id'] != $user_id, "不是你的评论", -1);
        CommentModel::_()->delete($id);
        ActionLogModel::_()->log("{$user['username']} 删除评论成功");
    }
}
