# PHP Migration

## TODO
- 是否检查的原则
    宁可错杀一万也不错过一个
- 程度上的原则
    - 新版本中会导致崩溃的 (Fatal)
    - 新版本中产生行为变化的 (Warning, Notice)
    - 新版本中废弃但未移除的功能 (Deprecated)
    - 新版本中引入的新功能、特性 (New)
        提供建议性的信息
- ChangeVisitor改名为CheckVisitor
- 做一个功能助手类的visitor，负责以下功能
    - 类继承关系记录
    - 变量在scope内的类型推测，值记录

### TODO 8
- Output using markdown
    - 引用注释
- 用statis还是self，是否有必要用静态
    同一个change是否可能有多个实例？比如在两个不同set中

### TODO 9
- 完成5.5的检查后重构全部Change
- 目录结构
    - Abstract类放在哪（同层|上层|专门目录）
        之前很多时候叫基类BaseXXX，其实是个偷懒的叫法和用法
        很多Base类其实都包含了：抽象或接口的约定限制(abstract)，快捷方法(trait)，原形定义
        按照Laravel的套路拆开吧
    - 目录是否为复数名称
- 遵守PSR标准
- 多行注释的规范格式
