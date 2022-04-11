## 用户信息管理

> 维护人员：**Kang**  
> 创建时间：2022-03-30

### 接口简介

用户信息管理

### Method

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;get()&nbsp;<font color="#888">【获取用户信息】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;create()&nbsp;<font color="#888">【添加一条用户信息】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;change()&nbsp;<font color="#888">【修改用户信息】</font>

---

### Detail

```
get(int $id):bool

```

获取用户信息

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

添加一条用户信息

---

##### Return Value

---

&emsp;bool

```
change(int $id):bool

```

修改用户信息

---

##### Parameters

| 参数名 | 类型 | 必填 | 描述    |
| :----- | :--- | :--- | :------ |
| $id    | int  | 是   | 用户 id |

##### Return Value

---

&emsp;bool
