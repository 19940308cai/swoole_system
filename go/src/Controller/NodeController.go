package Controller

import (
	"net/http"
	"Bootstrap"
	"Lib"
	"github.com/garyburd/redigo/redis"
)

type NodeController struct {
}

func (self *NodeController) GetNode(response http.ResponseWriter, request *http.Request) {
	response.Header().Set("Content-Type", "application/json")
	if request.Method != "POST" {
		response.Write(Bootstrap.MakeResponseBody(Bootstrap.ERROR_NUMBER, "faild Method"))
	} else {
		request.ParseForm()
		//检查权限
		if false == self._checkAuth(request) {
			response.Write(Bootstrap.MakeResponseBody(Bootstrap.ERROR_NUMBER, "no auth"))
		} else {
			//获取结点
			server_type := request.Form.Get("user_type")
			node_ip := self._getNodeByKey(server_type)
			data_map := make(map[string]string)
			if len(node_ip) > 0 {
				data_map["node_ip"] = node_ip
			}
			response.Write(Bootstrap.MakeResponseBody(Bootstrap.SUCCESS_NUMBER, data_map))
		}
	}
}

func (self *NodeController) _checkAuth(request *http.Request) bool {
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
		return false
	} else {
		conn := Lib.Pool.Get()
		isset, _ := redis.Bool(conn.Do("HEXISTS", cache_key, uid))
		conn.Close()
		return isset
	}
}

func (self *NodeController) _getNodeByKey(server_type string) string {
	var NodeIp string
	server_call_key := Bootstrap.HEARTBEAT_TABLE_KEY_TYPE + server_type
	conn := Lib.Pool.Get()
	reply, err := conn.Do("zrange", server_call_key, 0, 1000, "withscores")
	if err != nil {
		return NodeIp
	}
	reply_map, _ := redis.StringMap(reply, err)
	for ip, _ := range reply_map {
		NodeIp = ip
		break
	}
	defer conn.Close()
	return NodeIp
}

func (self *NodeController) ShowNodes(response http.ResponseWriter, request *http.Request) {

}

func (self *NodeController) DelNode(response http.ResponseWriter, request *http.Request) {

}
