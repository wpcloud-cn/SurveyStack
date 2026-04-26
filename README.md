# SurveyStack

SurveyStack 是一个基于 PHP 7.4+ 的开源在线问卷系统（无框架）。

## 功能
- 安装向导（MySQL/SQLite 二选一）
- 问卷发布、分页作答、一次性提交限制（IP/用户）
- 用户注册/登录/邮箱激活、个人中心与历史提交
- 后台管理：问卷、题目、结果、导出 CSV、用户、站点设置
- 基础安全：PDO 预处理、CSRF、password_hash

## 安装
1. 将目录部署到 Web 根目录或子目录。
2. 访问 `install.php` 并完成安装。
3. 安装完成后会生成 `config.php` 和 `install.lock`。

## 目录
见题目要求结构。

## 默认管理员
安装时创建。
