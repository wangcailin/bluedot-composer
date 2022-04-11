## 用户登录

> 维护人员：**Kang**  
> 创建时间：2022-03-29

### 接口简介

CMS 后台登录

### Method

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;login()&nbsp;<font color="#888">【check 用户账号密码执行登录操作】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;loginfailureCount()&nbsp;<font color="#888">【查询用户登录状态】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;loginfailure()&nbsp;<font color="#888">【记录一次用户登录失败记录】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;logintime()&nbsp;<font color="#888">【增加一条登录操作记录】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;currentUser()&nbsp;<font color="#888">【获取当前用户信息】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;refresh()&nbsp;<font color="#888">【刷新用户 token】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;respondWithToken()&nbsp;<font color="#888">【重新处理 token】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;create()&nbsp;<font color="#888">【新增一条用户信息记录】</font>

---

&emsp;array&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;getPublicToken()&nbsp;<font color="#888">【生成用户对应 token】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;passwordReset()&nbsp;<font color="#888">【重新设置账号密码】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;bindMail()&nbsp;<font color="#888">【绑定用户邮箱】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;verifyBindMail()&nbsp;<font color="#888">【用户解绑邮箱验证】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;bindSms()&nbsp;<font color="#888">【绑定用户手机号】</font>

---

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;verifyBindSms()&nbsp;<font color="#888">【用户手机号解绑验证】</font>

---

### Detail

```
login():bool

```

check 用户账号密码执行登录操作

---

##### Parameters

| 参数名   | 类型   | 必填 | 描述                                                     |
| :------- | :----- | :--- | :------------------------------------------------------- |
| type     | string | 是   | 类型：account=账号密码,mail=邮箱验证码,mobile=短信验证码 |
| username | string | 否   | 用户名                                                   |
| password | string | 否   | 密码                                                     |
| email    | string | 否   | 邮箱                                                     |
| code     | string | 否   | 验证码                                                   |
| phone    | string | 否   | 手机号                                                   |

##### Return Value

---

&emsp;bool

```
loginfailureCount(array $credentials):bool

```

查询用户登录状态

---

##### Parameters

| 参数名       | 类型  | 必填 | 描述     |
| :----------- | :---- | :--- | :------- |
| $credentials | array | 是   | 用户信息 |

##### Return Value

---

&emsp;bool

```
loginfailure(array $credentials):bool

```

记录一次用户登录失败记录

---

##### Parameters

| 参数名       | 类型  | 必填 | 描述     |
| :----------- | :---- | :--- | :------- |
| $credentials | array | 是   | 用户信息 |

##### Return Value

---

&emsp;bool

```
logintime():bool

```

增加一条登录操作记录

---

```
currentUser():array

```

获取当前用户信息

---

##### Return Value

---

&emsp;array

```
refresh():array

```

刷新用户 token

---

##### Return Value

---

&emsp;array

##### 返回正确 JSON 示例

```javascript
{
    "state": {
        "code": 10200,
        "msg": "ok"
    },
    "data": {
        "access_token": "",  //流水id
        "token_type": "bearer",  //用户名称
        "expires_in": 1648611704,  //过期时间
    }
}
```

```
respondWithToken():array

```

重新处理 token

---

##### Return Value

---

&emsp;array

##### 返回正确 JSON 示例

```javascript
{
    "state": {
        "code": 10200,
        "msg": "ok"
    },
    "data": {
        "access_token": "",  //用户token
        "token_type": "bearer",  //用户名称
        "expires_in": 1648611704,  //过期时间
    }
}
```

```
create():bool

```

重新处理 token

---

##### Parameters

| 参数名   | 类型   | 必填 | 描述   |
| :------- | :----- | :--- | :----- |
| username | string | 否   | 用户名 |
| password | string | 否   | 密码   |
| nickname | string | 否   | 昵称   |

##### Return Value

---

&emsp;bool

```
getPublicToken():array

```

生成用户对应 token

---

##### Return Value

---

&emsp;array

##### 返回正确 JSON 示例

```javascript
{
    "state": {
        "code": 10200,
        "msg": "ok"
    },
    "data": {
        "access_token": "",  //用户token
    }
}
```

```
passwordReset():bool

```

重新设置账号密码

---

##### Parameters

| 参数名           | 类型   | 必填 | 描述     |
| :--------------- | :----- | :--- | :------- |
| password         | string | 否   | 密码     |
| confirm_password | string | 否   | 确认密码 |

##### Return Value

---

&emsp;bool

```
bindMail():bool

```

绑定用户邮箱

---

##### Parameters

| 参数名 | 类型   | 必填 | 描述   |
| :----- | :----- | :--- | :----- |
| email  | string | 否   | 邮箱   |
| code   | string | 否   | 验证码 |
| action | string | 否   | 操作   |

##### Return Value

---

&emsp;bool

```
verifyBindMail():bool

```

用户解绑邮箱验证

---

##### Parameters

| 参数名 | 类型   | 必填 | 描述   |
| :----- | :----- | :--- | :----- |
| email  | string | 否   | 邮箱   |
| code   | string | 否   | 验证码 |
| action | string | 否   | 操作   |

##### Return Value

---

&emsp;bool

```
bindSms():bool

```

绑定用户手机号

---

##### Parameters

| 参数名 | 类型   | 必填 | 描述   |
| :----- | :----- | :--- | :----- |
| phone  | string | 否   | 手机号 |
| code   | string | 否   | 验证码 |
| action | string | 否   | 操作   |

##### Return Value

---

&emsp;bool

```
verifyBindSms():bool

```

用户手机号解绑验证

---

##### Parameters

| 参数名 | 类型   | 必填 | 描述   |
| :----- | :----- | :--- | :----- |
| phone  | string | 否   | 手机号 |
| code   | string | 否   | 验证码 |
| action | string | 否   | 操作   |

##### Return Value

---

&emsp;bool
