package main

import (
	"net/http"
	"github.com/garyburd/redigo/redis"
	"time"
	"fmt"
	"encoding/json"
)



func main() {
	go func() {
		node := make(map[string]string)
		conn, _ := redis.Dial("tcp", ":6379")
		psc := redis.PubSubConn{conn}
		psc.Subscribe("nodePool")
		for {
			switch v := psc.Receive().(type) {
			case redis.Message: //单个订阅subscribe
				json.Unmarshal(v.Data, &node)
				fmt.Println(node)
				fmt.Printf("node: %s\n", node["address"])
			case error:
				return

			}
		}
	}()
	time.Sleep(time.Second * 1000)
	http.HandleFunc("/get_node", func(w http.ResponseWriter, r *http.Request) {

	})
	http.ListenAndServe(":10000", nil)
}
