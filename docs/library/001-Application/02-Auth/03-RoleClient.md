## 用户角色管理

> 维护人员：**Kang**  
> 创建时间：2022-03-30

### 接口简介

用户角色管理

### Method

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;get()&nbsp;<font color="#888">【获取用户角色信息】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;update()&nbsp;<font color="#888">【修改角色信息】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;create()&nbsp;<font color="#888">【添加一条角色信息】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;getSelect()&nbsp;<font color="#888">【获取所有角色信息】</font>

---

### Detail

```
get(int $id):array

```

获取用户角色信息

---

##### Parameters

| 参数名 | 类型 | 必填 | 描述    |
| :----- | :--- | :--- | :------ |
| $id    | int  | 是   | 用户 id |

##### Return Value

---

&emsp;array

```
update(int $id):bool

```

更新用户角色信息

---

##### Parameters

| 参数名 | 类型 | 必填 | 描述    |
| :----- | :--- | :--- | :------ |
| $id    | int  | 是   | 用户 id |

##### Return Value

---

&emsp;bool

```
create():bool

```

添加一条用户角色信息

---

##### Return Value

---

&emsp;bool

```
getSelect():array

```

获取所有用户角色信息

---

##### Return Value

---

&emsp;array
