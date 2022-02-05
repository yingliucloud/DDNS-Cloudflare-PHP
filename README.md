# DDNS-Cloudflare-PHP

使用PHP编写，基于CloudflareAPI的DDNS

## 支持（已测试）
 - 中国电信（无需运行本程序设备拨号）

## 理论支持
 - 拨号服务器

# 快速上手

## 修改配置
$getIpUrl >公网Ip获取地址,上传ip.php到公网可访问的服务器/虚拟主机

$zoneId >Cloudflare 区域ID(控制面板获取)

$accountId >Cloudflare 账户ID（控制面板获取）

[控制面板](https://dash.cloudflare.com/)


$globalToken > [Cloudflare全局Token](https://dash.cloudflare.com/profile/api-tokens)

$authEmail >登录Cloudflare的邮箱

将以上配置在index.php相应的位置上修改.

## 安装扩展
运行本程序，需要php curl扩展支持
请自行编译安装
 - 如果是apt安装的php，可使用`sudo apt-get install php-curl` 安装此扩展

## 运行

`php index.php`

也可设置定时任务

 - Linux(Debian11√)

>在 `/etc/crontab` 中添加
>
>`*/5 * * * * root php /dir/dir/dir/more_dir/index.php`
>
>如果这样设置，程序将每五分钟执行一次
>具体计算方法可参照[Crontab.Guru](https://crontab.guru/)
>
>有些情况下，可能需要带上php版本号执行，如：
>
>`php7.4 /DDNS-Cloudflare-PHP/index.php`

# To Do
 - 完全取消All In One
 - 邮件通知
 - 钉钉通知
 - 添加域名
 - 远程更新域名信息





