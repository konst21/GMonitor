##Demo
http://gmonitor.nottes.net/gmonitor/

##About code
You can use this  
- as whole app,  
- use class `GMonitor` as component of Yii2 framework
- use class `GMonitor` (placed in `\components\GMonitor.php`) as standalone class in your app
Detailed info about each method of `GMonitor` class view in PHPDoc comments  
## Deploy whole GMonitor app
- copy code to your web directory
- change Gearman host (`default 127.0.0.1`) and port (`default 4730`) in `\components\Gmonitor.php`
- enjoy! GMonitor app is available by `<web path>/` URL 


##Usage
1) Place your functions to `/commands/funtions.php` file  
2) Start needed count of workers:  
- manually by `(root web path of app)/`: redirect to ->
- or manually by `(root web path of app>)/gmonitor/`
- or by API (see below)
3) Profit!

## HTTP API

You can control tasks/queue and workers by HTTP API.  
All link are relative to the root web path of application  


|URL|Correct Response|
|----|----|
|`/api/function_status`|JSON            |
|`/api/worker_count`|int            |
|`/api/worker_start`|'ok'            |
|`/api/worker_stop`|'ok'            |
|`/api/Reset_function_queue`|'ok'|
|`/api/Reset_all_queue`|'ok'|

Detailed information about each API method view in `/controllers/ApiController.php` file

 
