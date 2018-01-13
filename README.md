## HTTP API

You can control tasks/queue and workers by HTTP API

|Command descr.      |URL                         |Correct Response                         |
|----------------|-------------------------------|-----------------------------|
|Stop all workers|`/api/worker_stop`            |'ok'            |
|Start workers         |`http://slyii.nottes.net/api/worker_start?count=<count workers to start>`|'ok'            |
|All info about queue: functions in queue, current working, forkers count          |`"Isn't this fun?"`            |JSON, array format view in file GMonitor.php:147-173                                                                                                         
                                                                                                                                |
|          |`            |            |