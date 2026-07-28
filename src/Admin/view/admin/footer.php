<?php
/**
 * DuckPhp Admin System - Footer
 */
?>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 侧边栏子菜单切换
        function toggleSubMenu(link) {
            const subMenu = link.nextElementSibling;
            const arrow = link.querySelector('.arrow');
            if (subMenu) {
                subMenu.classList.toggle('open');
                if (arrow) arrow.classList.toggle('open');
            }
        }
        
        // 自动展开当前页面所在的子菜单
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sub-menu .nav-link').forEach(function(link) {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                    const parent = link.closest('.sub-menu');
                    if (parent) {
                        parent.classList.add('open');
                        const parentLink = parent.previousElementSibling;
                        if (parentLink) {
                            const arrow = parentLink.querySelector('.arrow');
                            if (arrow) arrow.classList.add('open');
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
