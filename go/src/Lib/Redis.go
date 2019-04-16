package Lib

import (
	"time"
	"github.com/garyburd/redigo/redis"
)

var Pool *redis.Pool

func init() {
	Pool = &redis.Pool{
		MaxIdle:     10,
		MaxActive:   0,
		IdleTimeout: 240 * time.Second,
		Wait:        true,
		Dial: func() (redis.Conn, error) {
			con, err := redis.Dial("tcp", ":6379")
			if err != nil {
				return nil, err
			}
			return con, nil
		},
	}
}
