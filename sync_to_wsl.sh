#!/bin/bash
#==============================================================================
# DuckAdmin 同步脚本 - 在 WSL 里运行
#
# 使用方式（在 WSL 终端执行）:
#   bash /mnt/e/ProjectGoat/DuckAdmin/sync_to_wsl.sh            # 只同步代码（推荐）
#   bash /mnt/e/ProjectGoat/DuckAdmin/sync_to_wsl.sh --with-state  # 连 runtime/database 一起同步
#   bash /mnt/e/ProjectGoat/DuckAdmin/sync_to_wsl.sh --lint       # 同步并语法检查所有 php
#
# 说明:
#   * 默认不同步 runtime/ 与 database/，避免覆盖 WSL 测试环境的安装状态与数据库
#   * 默认不同步 vendor/，改过 composer.json 时再执行 composer install
#   * 用 tar 做排除（cp 没有 --exclude）
#==============================================================================

set -e

# 配置
SOURCE_PATH="/mnt/e/ProjectGoat/DuckAdmin"   # Windows 源目录
TARGET_PATH="/DATA/ext4/duckadmin"           # WSL 目标目录
DOMAIN="bak-duckadmin.duckphp-local.com"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

WITH_STATE=false
DO_LINT=false
for arg in "$@"; do
    case "$arg" in
        --with-state) WITH_STATE=true ;;
        --lint)       DO_LINT=true ;;
        -h|--help)
            sed -n '2,14p' "$0"
            exit 0
            ;;
        *)
            echo -e "${RED}未知参数: $arg${NC}"
            exit 1
            ;;
    esac
done

echo -e "${CYAN}============================================${NC}"
echo -e "${CYAN}  DuckAdmin 同步到 WSL 测试环境${NC}"
echo -e "${CYAN}============================================${NC}"
echo ""

#---------- 1. 检查源目录 ----------
echo -e "${GREEN}[1/5] 检查源目录...${NC}"
if [[ ! -d "${SOURCE_PATH}" ]]; then
    echo -e "  ${RED}✗ 源目录不存在: ${SOURCE_PATH}${NC}"
    exit 1
fi
echo "  源:   ${SOURCE_PATH}"
echo "  目标: ${TARGET_PATH}"
if [[ "$WITH_STATE" == "true" ]]; then
    echo -e "  ${YELLOW}[模式] 含 runtime/database 状态${NC}"
else
    echo -e "  ${YELLOW}[模式] 仅代码（保留 WSL 的 runtime/database）${NC}"
fi

#---------- 2. 构建排除列表 ----------
echo ""
echo -e "${GREEN}[2/5] 准备同步...${NC}"
mkdir -p "${TARGET_PATH}"

TAR_EXCLUDES=(
    --exclude='.git'
    --exclude='.reasonix'
    --exclude='.vscode'
    --exclude='vendor'
    --exclude='composer.lock'
    --exclude='*.log'
    --exclude='.DS_Store'
    --exclude='Thumbs.db'
    # 环境相关的配置：各环境自己维护，不要被同步覆盖
    # (WSL 侧要打开 duckcoverage_enable 并设置 duckcoverage_web_base_url)
    --exclude='config/DuckPhpSettings.config.php'
)
if [[ "$WITH_STATE" == "false" ]]; then
    TAR_EXCLUDES+=(--exclude='runtime' --exclude='database' --exclude='*.db')
else
    TAR_EXCLUDES+=(--exclude='*.db')
fi
printf '  %s\n' "${TAR_EXCLUDES[@]}"

#---------- 3. 同步 ----------
echo ""
echo -e "${GREEN}[3/5] 同步文件...${NC}"
if command -v rsync >/dev/null 2>&1; then
    # 用 rsync --delete 做「单向镜像」：源里删掉的文件，目标里也删掉。
    # 注意：--delete 默认不会删除被 --exclude 排除的文件，
    # 所以 vendor/ runtime/ database/ 与环境配置都安全。
    rsync -a --delete "${TAR_EXCLUDES[@]}" "${SOURCE_PATH}/" "${TARGET_PATH}/"
    echo -e "  ${GREEN}✓${NC} 文件同步完成（rsync 镜像）"
else
    cd "${SOURCE_PATH}"
    tar "${TAR_EXCLUDES[@]}" -cf - . | tar -xf - -C "${TARGET_PATH}"

    # 没有 rsync，就自己补一遍「删除」：目标里存在、源里已不存在的文件要清掉。
    # 为什么必须删：目标里残留的 php 文件仍在覆盖测试的 path_src 里，
    # 会被算进覆盖率分母，让报告数字失真。
    # 下面 find 的排除项必须与上面的 TAR_EXCLUDES 一一对应。
    # 注意 -path 是**精确匹配**：只写 './runtime' 只能匹配目录自身，
    # 匹配不到 './runtime/xxx'。必须再加 './runtime/*'，否则这个"补删除"的
    # 步骤会把 WSL 侧 runtime/ 下的采集数据与报告全删掉（实测踩过：dump 与
    # report.json/report.jsonl 每同步一次就没了，而 *.log/*.db 因为
    # 用的是 -name 才幸免）。
    FIND_EXCLUDES=(
        -path './.git' -o -path './.git/*'
        -o -path './.reasonix' -o -path './.reasonix/*'
        -o -path './.vscode' -o -path './.vscode/*'
        -o -path './vendor' -o -path './vendor/*'
        -o -name 'composer.lock' -o -name '*.log' -o -name '.DS_Store' -o -name 'Thumbs.db'
        -o -path './config/DuckPhpSettings.config.php'
    )
    if [[ "$WITH_STATE" == "false" ]]; then
        FIND_EXCLUDES+=(-o -path './runtime' -o -path './runtime/*' -o -path './database' -o -path './database/*' -o -name '*.db')
    else
        FIND_EXCLUDES+=(-o -name '*.db')
    fi
    ( cd "${SOURCE_PATH}" && find . -type f ! \( "${FIND_EXCLUDES[@]}" \) -printf '%P\n' | sort ) > /tmp/.dsh_sync_src
    ( cd "${TARGET_PATH}" && find . -type f ! \( "${FIND_EXCLUDES[@]}" \) -printf '%P\n' | sort ) > /tmp/.dsh_sync_tgt
    comm -13 /tmp/.dsh_sync_src /tmp/.dsh_sync_tgt > /tmp/.dsh_sync_del
    DEL_COUNT=$(grep -c . /tmp/.dsh_sync_del || true)
    while IFS= read -r rel; do
        [ -n "$rel" ] && rm -f "${TARGET_PATH}/${rel}"
    done < /tmp/.dsh_sync_del
    echo -e "  ${GREEN}✓${NC} 文件同步完成（tar 覆盖 + 清理 ${DEL_COUNT} 个源里已删除的文件）"
fi

#---------- 4. vendor 检查 ----------
echo ""
echo -e "${GREEN}[4/5] 检查依赖...${NC}"
if [[ ! -d "${TARGET_PATH}/vendor" ]]; then
    echo -e "  ${YELLOW}⚠ vendor 不存在，执行 composer install${NC}"
    ( cd "${TARGET_PATH}" && composer install --no-interaction --prefer-dist )
    echo -e "  ${GREEN}✓${NC} 依赖安装完成"
else
    echo -e "  ${GREEN}✓${NC} vendor 已存在（改过 composer.json 时请手动 composer install）"
fi

#---------- 5. 权限 + 可选语法检查 ----------
echo ""
echo -e "${GREEN}[5/5] 收尾...${NC}"
cd "${TARGET_PATH}"
chmod -R 777 runtime database 2>/dev/null || true
echo -e "  ${GREEN}✓${NC} runtime/database 权限已放开"

if [[ "$DO_LINT" == "true" ]]; then
    echo "  语法检查:"
    FAIL=0
    while IFS= read -r f; do
        if ! php -l "$f" > /dev/null 2>&1; then
            echo -e "    ${RED}✗ $f${NC}"
            php -l "$f" || true
            FAIL=1
        fi
    done < <(find src view public -name '*.php' -type f 2>/dev/null)
    if [[ "$FAIL" == "0" ]]; then
        echo -e "    ${GREEN}✓${NC} 所有 php 文件语法通过"
    fi
fi

echo ""
echo -e "${CYAN}============================================${NC}"
echo -e "${CYAN}  同步完成！${NC}"
echo -e "${CYAN}============================================${NC}"
echo ""
echo "  首页:     http://${DOMAIN}/"
echo "  后台:     http://${DOMAIN}/admin/"
echo "  phpinfo:  http://${DOMAIN}/phpinfo.php"
echo ""
echo "  提示: 管理员/用户系统在首页选择，安装入口为 <前缀>/install"
