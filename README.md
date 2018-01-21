## HTTP API

You can control tasks/queue and workers by HTTP API
All link are relative to the root web path of application

|Command descr.      |URL                         |Correct Response                         |
|----------------|-------------------------------|-----------------------------|
|Stop all workers|`/api/worker_stop`            |'ok'            |
|Start workers         |`/api/worker_start?count=<count workers to start>`|'ok'            |
|All info about queue: functions in queue, current working, forkers count          |``            |JSON, array format view in file GMonitor.php:147-173                                                                                                         
                                                                                                                                |
|          |`            |            |