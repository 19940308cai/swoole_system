package Bootstrap

import "encoding/json"

func MakeResponseBody(code int, data interface{}) []byte {
	var ResponseBody map[string]interface{} = make(map[string]interface{})
	ResponseBody["code"] = code
	if code == SUCCESS_NUMBER {
		ResponseBody["message"] = SUCCESS_STRING
	} else {
		ResponseBody["message"] = ERROR_STRING
	}
	ResponseBody["data"] = data
	json_byte, _ := json.Marshal(ResponseBody)
	return json_byte
}
