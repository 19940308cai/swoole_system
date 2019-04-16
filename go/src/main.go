package main

import (
	"net/http"
	"log"
	"Controller"
	"Task"
)

const IP = "127.0.0.1"

const PORT = "10000"

func main() {
	heartTask := Task.NewHeartBeatTask()
	heartTask.Run()
	http.HandleFunc("/checkAuth", new(Controller.AuthController).CheckAuth)
	http.HandleFunc("/getNode", new(Controller.NodeController).GetNode)
	http.HandleFunc("/DelNode", new(Controller.NodeController).DelNode)
	http.HandleFunc("/ShowNodes", new(Controller.NodeController).ShowNodes)
	log.Fatal(http.ListenAndServe(IP+":"+PORT, nil))
}
