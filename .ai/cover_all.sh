#!/bin/bash
#==============================================================================
# DuckAdmin 覆盖测试：全量 —— 七个应用各跑一场，再合并报告
#
# 为什么不是一场跑完：每个应用的清单都是「自己安装 + 自己登录」的，
# 挤在同一场回放、同一个会话/库里会互相踩（实测：一场跑完只有 34%，
# 七个分开跑再合并是 70%+）。
#
# 原理：--flag = 某个应用的 controller_url_prefix（去掉结尾斜杠），
#       根 lister（System\TestLister::_GetTestList）只回放那一个应用；
#       每个应用一场，最后 `cover --report <七个组名>` 合并成一份。
#
# 用法（在项目根目录执行）:
#   bash .ai/cover_all.sh
#   REPORT_ARGS="--jsonl=runtime/DuckCoverage/report.jsonl --jsonl-no-timestamp" \
#       bash .ai/cover_all.sh          # 顺便产出机器可读的 report.jsonl
#
# 单测一个应用用 .ai/cover_one.sh。
# 旧的分组模式 .ai/cover_flags.sh 的组表仍是旧短名标记（admin+demo …），
# 现在会大面积跳过 —— 见那个脚本里的 TODO。
#==============================================================================
set -u
cd "$(dirname "$0")/.." || exit 1
[ -f bin/cli.php ] || { echo "请在项目根目录执行（找不到 bin/cli.php）"; exit 1; }

APPS="admin admin_laravel admin_thinkphp single_admin demo_users user blog"
NAMES=""
rm -rf runtime/DuckCoverage
chmod 777 runtime 2>/dev/null

echo "===== 七个应用各跑一场 ====="
for app in $APPS; do
    g="all_$app"
    NAMES="$NAMES $g"
    php bin/cli.php cover --watch "$g" --flag="$app" >/dev/null 2>&1
    php bin/cli.php cover --play  --flag="$app" > "runtime/play_$g.txt" 2>&1
    php bin/cli.php cover --stop  >/dev/null 2>&1
    req=$(grep -c 'explainWeb' "runtime/play_$g.txt")
    warn=$(grep -ci 'warning' "runtime/play_$g.txt")
    bad=$(grep -cE 'TypeError|Fatal error|ErrorException' "runtime/play_$g.txt")
    apps=$(grep -oE 'COMMENT APP [^ ]+' "runtime/play_$g.txt" | sed 's/COMMENT APP //; s/\x1b\[[0-9;]*m//g' | sort -u | wc -l)
    printf "  %-16s 请求=%-4s 警告=%-3s 异常=%-3s 回放应用数=%s\n" "$app" "$req" "$warn" "$bad" "$apps"
    if [ "$bad" != "0" ]; then
        grep -oE '<h3>[^<]*</h3>' "runtime/play_$g.txt" | head -2 | sed 's/^/      /'
    fi
done

echo "===== 合并报告:$NAMES"
# 默认产出 jsonl，这样 .ai/coverage_gaps.php 才能给出总计；要换参数就传 REPORT_ARGS。
ARGS="${REPORT_ARGS:---jsonl=runtime/DuckCoverage/report.jsonl --jsonl-no-timestamp}"
php bin/cli.php cover --report $NAMES $ARGS 2>&1 | tail -3

echo "===== 总计 ====="
php .ai/coverage_gaps.php --max=2 | tail -4
