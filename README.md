# PHP Migration


## TODO

### TODO 0
- Document -> Check -> Spot
    - 制定统一的便于检索的注释
        类似：xxxx问题由yyyy检查可能会报出zzzz错误
    - Spot级别分类
        tip, fatal, deprecated
- Output using markdown
    - 引用注释

### TODO 8
- 用statis还是self，是否有必要用静态

### TODO 9
- 目录结构
    - Abstract类放在哪（同层|上层|专门目录）
        之前很多时候叫基类BaseXXX，其实是个偷懒的叫法和用法
        很多Base类其实都包含了：抽象或接口的约定限制(abstract)，快捷方法(trait)，原形定义
        按照Laravel的套路拆开吧
    - 目录是否为复数名称
- 遵守PSR标准
- 多行注释的规范格式
