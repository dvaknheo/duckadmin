#!/bin/bash
#==============================================================================
# DuckAdmin 覆盖测试：单独测一个子应用
#
# 原理：--flag 的取值就是某个子应用的 controller_url_prefix（去掉结尾斜杠），
#       根 lister（System\TestLister::_GetTestList）拿它匹配
#       options['app'][*]['controller_url_prefix']，匹配上就只 #INCLUDE_CHILD 那一个应用；
#       匹配不上、或没有 flag，才回落到全量清单（见 .ai/cover_flags.sh）。
#
# 可用前缀（= 各子应用的 controller_url_prefix，去掉结尾斜杠）:
#   admin / admin_laravel / admin_thinkphp / single_admin / demo_users / user / blog
#
# 用法（在项目根目录执行）:
#   bash .ai/cover_one.sh blog                 # 只测 SimpleBlog，组名默认 one_blog
#   bash .ai/cover_one.sh user e_user_solo     # 只测 User，组名自定义
#   REPORT_ARGS="--jsonl=runtime/DuckCoverage/report.jsonl" bash .ai/cover_one.sh blog
#
# 注意:
#   - flag 不要带结尾斜杠 —— 带斜杠匹配不上，会静默跑成"全量清单"；
#   - 报告目录与库名都以「组名」打头：runtime/DuckCoverage/<组名>.report/、
#     runtime/DuckCoverage/<组名>[-<flag>]-<应用名>.db —— 与全量跑互不覆盖；
#   - 只有 user / blog 这两个 flag 会顺手把提供者配置写成默认值
#     （toDefaultAdmin() = SingleAdmin、toDefaultUser() = DemoUsers）；
#     admin / admin_laravel / admin_thinkphp **不会** —— 单测它们之前，
#     先确认动态配置里的管理员提供者就是它自己（后台首页表单切换），
#     否则 Admin::_() 会指向别的系统，测出来的数字会误导你。
#
# 回放日志 runtime/play_<组名>.txt 里，每条 #INCLUDE_CHILD 会展开成一行
# "COMMENT APP <类名>" —— 单应用跑就应当只有一行，这是最直接的证据。
#==============================================================================

set -u
cd "$(dirname "$0")/.." || exit 1
[ -f bin/cli.php ] || { echo "请在项目根目录执行（找不到 bin/cli.php）"; exit 1; }

APP="${1:?用法: bash .ai/cover_one.sh <应用前缀> [组名]}"
GROUP="${2:-one_$APP}"

case "$APP" in
    admin|admin_laravel|admin_thinkphp)
        cur=$(php -r '$c = @include "config/DuckPhpSettings.config.php"; echo (string)($c["duckadmin_admin_provider"] ?? "");' 2>/dev/null)
        echo "注意: $APP 不会自动改提供者配置；当前 duckadmin_admin_provider = ${cur:-（读不到）}"
        echo "      单测它之前，先在后台首页把管理员提供者切到它，或先跑一次: bash .ai/cover_one.sh blog"
        ;;
esac

rm -rf "runtime/DuckCoverage/$GROUP"* "runtime/play_$GROUP.txt"
chmod 777 runtime 2>/dev/null

echo "---- 单独测 [$APP]  组名 [$GROUP]"
php bin/cli.php cover --watch "$GROUP" --flag="$APP" >/dev/null 2>&1
php bin/cli.php cover --play  --flag="$APP" > "runtime/play_$GROUP.txt" 2>&1
php bin/cli.php cover --stop >/dev/null 2>&1

req=$(grep -c 'explainWeb' "runtime/play_$GROUP.txt")
warn=$(grep -ci 'warning' "runtime/play_$GROUP.txt")
bad=$(grep -cE 'TypeError|Fatal error|ErrorException' "runtime/play_$GROUP.txt")
echo "     WEB 请求=$req  警告=$warn  异常=$bad"
if [ "$bad" != "0" ]; then
    echo "     首条异常: $(grep -oE '<h3>[^<]*</h3>' "runtime/play_$GROUP.txt" | head -1)"
fi

echo "     实际回放的子应用（#INCLUDE_CHILD 展开出的 COMMENT APP 行）:"
grep -oE 'COMMENT APP [^ ]+' "runtime/play_$GROUP.txt" | sed 's/COMMENT APP //; s/\x1b\[[0-9;]*m//g' | sort -u | sed 's/^/       /'

echo "---- 报告"
php bin/cli.php cover --report "$GROUP" ${REPORT_ARGS:-} 2>&1 | tail -3
