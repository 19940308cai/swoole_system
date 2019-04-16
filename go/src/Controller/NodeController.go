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
	if request.Method != "POST" {
		response.Write(Bootstrap.MakeResponseBody(Bootstrap.ERROR_NUMBER, "faild Method"))
	} else {
		request.ParseForm()
		server_type := request.Form.Get("type")
		node_ip := self._getNodeByKey(server_type)
		data_map := make(map[string]string)
		if len(node_ip) > 0 {
			data_map["node_ip"] = node_ip
		}
		response.Write(Bootstrap.MakeResponseBody(Bootstrap.SUCCESS_NUMBER, data_map))
	}
}

func (self *NodeController) _getNodeByKey(server_type string) string {
	var NodeIp string
	server_call_key := Bootstrap.HEARTBEAT_TABLE_KEY_TYPE + server_type
	conn := Lib.Pool.Get()
	reply, err := conn.Do("zrange", server_call_key+"zzz", 0, 1000, "withscores")
	if err == nil {
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
