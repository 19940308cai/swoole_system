package Task

import (
	"encoding/json"
	"log"
	"github.com/garyburd/redigo/redis"
	"time"
	"Lib"
	"Bootstrap"
)

type HeartBeatTask struct {
	pool *redis.Pool
}

func NewHeartBeatTask() *HeartBeatTask {
	return &HeartBeatTask{
		Lib.Pool,
	}
}

func (self *HeartBeatTask) Run() error {
	go func() {
		node := make(map[string]string)
		conn := self.pool.Get()
		psc := redis.PubSubConn{conn}
		psc.Subscribe("websocket_heart")
		for {
			switch v := psc.Receive().(type) {
			case redis.Message: //单个订阅subscribe
				json.Unmarshal(v.Data, &node)
				log.Println(node)
				log.Printf("Receive node heartBeat: type[%s] - address[%s] - address[%s] \n", node["type"], node["address"], node["port"])
				self.ReportTable(node)
			}
		}
	}()
	return nil
}

func (self *HeartBeatTask) ReportTable(node map[string]string) {
	conn := self.pool.Get()
	conn.Do("zadd", Bootstrap.HEARTBEAT_TABLE_KEY_TYPE+node["type"], time.Now().Unix(), node["address"]+":"+node["port"])
	conn.Flush()
	defer conn.Close()
}
