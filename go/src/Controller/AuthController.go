package Controller

import (
	"net/http"
	"Bootstrap"
	"Lib"
	"github.com/garyburd/redigo/redis"
)

type AuthController struct {
}

func (self *AuthController) CheckAuth(response http.ResponseWriter, request *http.Request) {
	if request.Method != "POST" {
		response.Write(Bootstrap.MakeResponseBody(Bootstrap.SUCCESS_NUMBER, false))
	} else {
		conn := Lib.Pool.Get()
		request.ParseForm()
		user_type := request.Form.Get("user_type")
		uid := request.Form.Get("uid")
		var cache_key string
		switch user_type {
		case Bootstrap.CUSTOMER:
			cache_key = Bootstrap.LOGIN_CUSTOMER_CACHE
			break
		case Bootstrap.PROVIDER:
			cache_key = Bootstrap.LOGIN_PROVIDER_CACHE
			break
		case Bootstrap.STORE:
			cache_key = Bootstrap.LOGIN_STORE_CACHE
			break
		}
		if len(cache_key) <= 0 {
			response.Write(Bootstrap.MakeResponseBody(Bootstrap.SUCCESS_NUMBER, false))
		}else{
			isset, _ := redis.Bool(conn.Do("SISMEMBER", cache_key, uid))
			conn.Close()
			response.Write(Bootstrap.MakeResponseBody(Bootstrap.SUCCESS_NUMBER, isset))
		}
	}
}
