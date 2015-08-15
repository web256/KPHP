<?php
class Timer
{
    // 保存所有定时任务
    public static $task = array();

    //定时间隔
    public static $time = 1;

    public static function run($time = null)
    {
        if ($time) {
            self::$time = $time;

        }

        self::installHandler();
        // 创建一个计时器，在指定的秒数后向进程发送一个SIGALRM信号。每次对 pcntl_alarm()的调用都会取消之前设置的alarm信号。
        pcntl_alarm(1);
    }

    /**
      *注册信号处理函数
      */
      public static function installHandler()
      {
          // 信号注册：当接收到SIGINT信号时，调用signalHandler()函数
          pcntl_signal(SIGALRM, array('Timer','signalHandler'));
      }

    /**
      *信号处理函数
      */
      public static function signalHandler()
      {
          echo "signalHandler\n";
          //一次信号事件执行完成后,再触发下一次
          pcntl_alarm(self::$time);
      }
}

Timer::run();

while(true) {

    sleep(1);
    // 接收到信号时，分发给信号处理事件
    pcntl_signal_dispatch();
}
?>