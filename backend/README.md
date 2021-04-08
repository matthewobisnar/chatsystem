Run application in Commandline and set public folder as root path:
```
php -S localhost:8080 -t public/
```

Test application on Postman with Json Type payload: 
```
{
    "process_code": "examProcessCode",
    "request_data": {
        "taken_written_exam": true,
        "taken_interview": false,
        "score": 90
    }
}
```
Postman:
```
https://www.getpostman.com/collections/066af3268ae8e59fe244
```