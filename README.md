# PHP Migration

This is a static analyzer for PHP version migration.

### Chinese - 中文

这是一个用于PHP版本迁移的静态分析器。

它的主要功能是检查当前代码在新版本PHP下的兼容性并提供相关的建议及处理方法。

**注意：该项目目前仍处于开发阶段**

#### 安装

首先，将整个项目clone到本地，并进入项目目录
```
git clone git@github.com:monque/PHP-Migration.git php-migration
cd php-migration
```

执行下面命令来安装 [Composer](https://getcomposer.org/download/)
```
curl -sS https://getcomposer.org/installer | php
```

最后通过Composer安装项目所需的依赖
```
php composer.phar install
```

你可以将执行目录添加到环境变量`PATH`中，这样就可以直接通过命令`phpmig`来运行本程序了
```
# 临时添加（登出后失效）
export PATH="`pwd`/bin:$PATH"

# 傻瓜式添加
echo "export PATH=\"`pwd`/bin:\$PATH\"" >> ~/.bashrc
```

#### 使用

执行以下命令即可进行代码版本兼容性的检查
```
phpmig ~/workspace/your-project
```
