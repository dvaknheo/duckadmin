#!/bin/bash
#==============================================================================
# DuckAdmin 覆盖测试：按「提供者」分组建档
#
# 背景：同一时刻只能有一个管理员提供者 + 一个用户提供者被选中（共享的 Admin/User 单例），
#       而各子应用的测试清单又用 admin_provider_enable / user_provider_enable 守卫自己，
#       所以「一个提供者一组」，最后用 cover --report 把所有组合并成一份报告。
#
# 用法（在项目根目录执行）:
#   bash .ai/cover_groups.sh                 # 跑全部组并出报告
#   bash .ai/cover_groups.sh e_user f_blog   # 只跑指定组
#   bash .ai/cover_groups.sh --report-only   # 不出报告，只跑组
#
# 前置条件（见 config/DuckPhpSettings.config.php.sample）：
#   - duckcoverage_enable = true
#   - duckcoverage_web_base_url 指向本环境站点
#   - php.ini 的 xdebug.mode 含 coverage（CLI 与 FPM 都要）
#==============================================================================

set -u
cd "$(dirname "$0")/.." || exit 1
[ -f bin/cli.php ] || { echo "请在项目根目录执行（找不到 bin/cli.php）"; exit 1; }

# 组名|管理员提供者|用户提供者
GROUPS_ALL=(
    "a_admin|DuckAdmin\\Admin\\System\\AdminApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "b_laravel|DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "c_think|DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "d_single|DuckAdmin\\SingleAdmin\\System\\SingleAdminApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "e_user|DuckAdmin\\Admin\\System\\AdminApp|DuckAdmin\\User\\System\\UserApp"
    "f_blog|DuckAdmin\\Admin\\System\\AdminApp|DuckAdmin\\User\\System\\UserApp"
)

DO_REPORT=1
WANTED=()
for a in "$@"; do
    case "$a" in
        --report-only) DO_REPORT=0 ;;
        -h|--help) sed -n '2,20p' "$0"; exit 0 ;;
        *) WANTED+=("$a") ;;
    esac
done

BASE=$(php -r '$c = @include "config/DuckPhpSettings.config.php"; echo rtrim((string)($c["duckcoverage_web_base_url"] ?? "http://admin.duckphp-local.com/"), "/");' 2>/dev/null)
[ -n "$BASE" ] || { echo "无法确定 duckcoverage_web_base_url"; exit 1; }
echo "站点基址: $BASE"

RUN=()
for row in "${GROUPS_ALL[@]}"; do
    name="${row%%|*}"
    if [ ${#WANTED[@]} -eq 0 ]; then RUN+=("$row"); continue; fi
    for w in "${WANTED[@]}"; do [ "$w" = "$name" ] && RUN+=("$row"); done
done
[ ${#RUN[@]} -gt 0 ] || { echo "没有匹配的组"; exit 1; }

DONE=()
SKIPPED=()
for row in "${RUN[@]}"; do
    IFS='|' read -r name ap up <<< "$row"
    echo "---- $name (admin=$ap user=$up)"
    php bin/cli.php cover --watch "$name" >/dev/null 2>&1

    # watch 必须真的生效,否则下面的 POST 会把提供者写进「上一组」的配置文件
    got=$(cat runtime/DuckCoverage/DuckCoverage.watching.txt 2>/dev/null)
    if [ "$got" != "$name" ]; then
        echo "     !! watch 未生效(watching=$got),跳过本组"
        SKIPPED+=("$name")
        continue
    fi

    # 提供者选择要写进「本组的」data 文件,所以必须在 watch 窗口内做;写不进去就重试一次
    pat=$(printf '%s' "$ap" | sed 's/\\/\\\\/g')
    ok=0
    for try in 1 2; do
        curl -s -X POST "$BASE/" --data-urlencode "admin=$ap" --data-urlencode "user=$up" -o /dev/null
        if grep -qF "$pat" "runtime/DuckCoverage/$name.DuckPhpData.config.json" 2>/dev/null; then ok=1; break; fi
        echo "     提供者未写入本组配置,重试"
    done
    if [ "$ok" != "1" ]; then
        echo "     !! 提供者写入失败,跳过本组"
        SKIPPED+=("$name")
        continue
    fi

    php bin/cli.php cover --play > "/tmp/play_${name}.txt" 2>&1
    req=$(grep -c 'explainWeb' "/tmp/play_${name}.txt")
    warn=$(grep -ci 'warning' "/tmp/play_${name}.txt")
    bad=$(grep -cE 'TypeError|Fatal error|ErrorException' "/tmp/play_${name}.txt")
    files=$(ls "runtime/DuckCoverage/$name" 2>/dev/null | wc -l)
    echo "     WEB 请求=$req  采集文件=$files  警告=$warn  异常=$bad"
    if [ "$bad" != "0" ]; then
        echo "     首条异常: $(grep -oE '<h3>[^<]*</h3>' "/tmp/play_${name}.txt" | head -1)"
    fi
    DONE+=("$name")
done
php bin/cli.php cover --stop >/dev/null 2>&1
[ ${#SKIPPED[@]} -gt 0 ] && echo "跳过的组: ${SKIPPED[*]}"

if [ "$DO_REPORT" = "1" ] && [ ${#DONE[@]} -gt 0 ]; then
    echo "---- 合并报告: ${DONE[*]}"
    # REPORT_ARGS 用来透传 cover --report 的新开关，例如：
    #   REPORT_ARGS="--jsonl=runtime/DuckCoverage/report.jsonl --jsonl-no-timestamp" bash .ai/cover_groups.sh
    php bin/cli.php cover --report "${DONE[@]}" ${REPORT_ARGS:-} 2>&1 | tail -3
fi
