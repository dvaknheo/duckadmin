<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;

/**
 * 我们这里只是偷懒一下啦。 放空类在这，省得 use .
 * 因为 C 也是这里。 要重载这里的动态方法，要用 Base::G(MyBase::G()); 而不是 XController::G(MyBase::G());
 */
class Base extends ProjectController
{
    public function __construct()
    {
        // 我们弄个小技巧，不允许直接访问，但我们可以创建一个实例填充，
        //if (static::CheckRunningController(self::class, static::class)) {
        //    return;
        //}
        //parent::__construct();
		//我们检查安装？
    }
	//

    /**
     * 无需登录及鉴权的方法
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 需要登录无需鉴权的方法
     * @var array
     */
    protected $noNeedAuth = [];

    /**
     * 数据限制
     * 例如当$dataLimit='personal'时将只返回当前管理员的数据
     * @var string
     */
    protected $dataLimit = null;



    /**
     * 返回格式化json数据
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return Response
     */
    protected function json(int $code, string $msg = 'ok', array $data = []): Response
    {
        return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }
	
	protected static function Sucess()
	{
		//
	}
    public function process(Request $request, callable $handler): Response
    {
        $controller = $request->controller;
        $action = $request->action;

        $code = 0;
        $msg = '';
        if (!Auth::canAccess($controller, $action, $code, $msg)) {
            if ($request->expectsJson()) {
                $response = json(['code' => $code, 'msg' => $msg, 'type' => 'error']);
            } else {
                if ($code === 401) {
                    $response = response(<<<EOF
<script>
    if (self !== top) {
        parent.location.reload();
    }
</script>
EOF
                    );
                } else {
                    $request->app = '';
                    $request->plugin = 'admin';
                    $response = view('common/error/403')->withStatus(403);
                }
            }
        } else {
            $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        }
        return $response;
    }
	
}
