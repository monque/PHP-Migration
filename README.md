## TODO 0
- Advice的设计
- Change的概念定义
    - 提供通用方法 filename
    - 是否可以检查多个特征 是
    - 是否与advice对应 否

## TODO 1
- 未实现的检查
    - call-time pass-by-reference
    - ini file
- 完善内置函数的获取方式
    - document html or xml
    - ext/basic_function.c

## TODO 9
- Abstract类放在哪（同层|上层|专门目录）
    之前很多时候叫基类BaseXXX，其实是个偷懒的叫法和用法
    很多Base类其实都包含了：抽象或接口的约定限制(abstract)，快捷方法(trait)，原形定义
    按照Laravel的套路拆开吧
- 使用PEAR中的getopt替代docopt
- PSR Header
