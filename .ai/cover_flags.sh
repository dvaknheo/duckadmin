#!/bin/bash
#==============================================================================
# DuckAdmin 覆盖测试（--flag 模式）：按「提供者」分组的采集
#
# 与 .ai/cover_groups.sh 的区别：
#   旧：每组都要「改写本组的 DuckPhpData 配置文件（首页 POST）→ 校验写入是否成功」，
#       再回放。配置文件是磁盘状态，天然有竞态，校验失败就跳过整组。
#   新：提供者不再靠配置文件，而是靠采集标记 —— duckcoverage 的 --flag 会把它
#       放进每个回放请求的请求头（X-DuckCoverage-Flag），应用侧读同一个值来决定
#       用哪个管理员/用户提供者。于是每组只差一个命令行参数，磁盘状态不用动。
#
# 安全前提（应用侧已加锁，见 DuckAdminApp::applyProviderFlag()）：
#   标记只在 settings 里 duckcoverage_enable 为真时才生效；生产环境没有这个键，
#   所以请求头不会影响认证。
#
# 用法（在项目根目录执行）:
#   bash .ai/cover_flags.sh                 # 跑全部组并出报告
#   bash .ai/cover_flags.sh e_user f_blog   # 只跑指定组
#   bash .ai/cover_flags.sh --report-only   # 只跑组，不出报告
#   REPORT_ARGS="--jsonl=runtime/DuckCoverage/report.jsonl --jsonl-no-timestamp" \
#       bash .ai/cover_flags.sh             # 顺便产出 report.jsonl
#
# 前置条件（见 config/DuckPhpSettings.config.php.sample）：
#   - duckcoverage_enable = true
#   - duckcoverage_web_base_url 指向本环境站点
#   - php.ini 的 xdebug.mode 含 coverage（CLI 与 FPM 都要）
#   - duckcoverage 版本需支持 --flag / getFlag()（本仓库 vendor 已带）
#==============================================================================

set -u
cd "$(dirname "$0")/.." || exit 1
[ -f bin/cli.php ] || { echo "请在项目根目录执行（找不到 bin/cli.php）"; exit 1; }

# 组名|采集标记|管理员提供者(用于校验)|用户提供者(用于校验)
# 标记里的短名 → 类名的对照在应用侧 DuckAdminApp::FLAG_PROVIDERS；
# 这里把类名再写一遍，是为了回放前能自己校验「标记真的生效了」。
GROUPS_ALL=(
    "a_admin|admin+demo|DuckAdmin\\Admin\\System\\AdminApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "b_laravel|laravel+demo|DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "c_think|think+demo|DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "d_single|single+demo|DuckAdmin\\SingleAdmin\\System\\SingleAdminApp|DuckAdmin\\DemoUsers\\System\\DemoUsersApp"
    "e_user|admin+user|DuckAdmin\\Admin\\System\\AdminApp|DuckAdmin\\User\\System\\UserApp"
    "f_blog|admin+user|DuckAdmin\\Admin\\System\\AdminApp|DuckAdmin\\User\\System\\UserApp"
)

DO_REPORT=1
WANTED=()
for a in "$@"; do
    case "$a" in
        --report-only) DO_REPORT=0 ;;
        -h|--help) sed -n '2,30p' "$0"; exit 0 ;;
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
    IFS='|' read -r name flag ap up <<< "$row"
    echo "---- $name (flag=$flag → admin=$ap user=$up)"
    php bin/cli.php cover --watch "$name" --flag="$flag" >/dev/null 2>&1

    # watch 必须真的生效，否则本组采集会落到上一组的目录里
    got=$(cat runtime/DuckCoverage/DuckCoverage.watching.txt 2>/dev/null)
    if [ "$got" != "$name" ]; then
        echo "     !! watch 未生效(watching=$got),跳过本组"
        SKIPPED+=("$name")
        continue
    fi

    # 标记是否真的生效：让应用自己回答「按这个标记解析出的提供者是谁」。
    # 不依赖首页 HTML 的写法（表单里并没有 selected 标记，靠文本匹配会误判）。
    # 注意 CLI 会先打印 duckcoverage 的提示和命令列表，所以只取 admin=/user= 那行。
    got_prov=$(DUCKCOVERAGE_FLAG="$flag" php .ai/provider_probe.php 2>/dev/null | grep -E '^(admin|user)=' | head -1)
    want_admin="admin=${ap##*\\}"
    want_user="user=${up##*\\}"
    if ! printf '%s' "$got_prov" | grep -qF "$want_admin" || ! printf '%s' "$got_prov" | grep -qF "$want_user"; then
        echo "     !! 标记未生效: 期望 [$want_admin  $want_user], 实际 [$got_prov]"
        SKIPPED+=("$name")
        continue
    fi

    php bin/cli.php cover --play --flag="$flag" > "runtime/play_${name}.txt" 2>&1
    req=$(grep -c 'explainWeb' "runtime/play_${name}.txt")
    warn=$(grep -ci 'warning' "runtime/play_${name}.txt")
    bad=$(grep -cE 'TypeError|Fatal error|ErrorException' "runtime/play_${name}.txt")
    files=$(ls "runtime/DuckCoverage/$name" 2>/dev/null | wc -l)
    echo "     WEB 请求=$req  采集文件=$files  警告=$warn  异常=$bad"
    if [ "$bad" != "0" ]; then
        echo "     首条异常: $(grep -oE '<h3>[^<]*</h3>' "runtime/play_${name}.txt" | head -1)"
    fi
    DONE+=("$name")
done
php bin/cli.php cover --stop >/dev/null 2>&1
[ ${#SKIPPED[@]} -gt 0 ] && echo "跳过的组: ${SKIPPED[*]}"

if [ "$DO_REPORT" = "1" ] && [ ${#DONE[@]} -gt 0 ]; then
    echo "---- 合并报告: ${DONE[*]}"
    php bin/cli.php cover --report "${DONE[@]}" ${REPORT_ARGS:-} 2>&1 | tail -3
fi
