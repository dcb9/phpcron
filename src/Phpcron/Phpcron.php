<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 14-8-1
 * Time: 上午1:12
 */

namespace Phpcron;
use Pagon\ChildProcess;
use Pagon\EventEmitter;

class Phpcron extends EventEmitter{
    const ROLE_MASTER = 0;
    const ROLE_BACKEND = 1;
    protected $_is_run = false;
    protected $options = array(
        'engine'=>'pdo',
    );
    public $role = array(
        self::ROLE_MASTER=>'主',
        self::ROLE_BACKEND=>'备主',
    );

    /**
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options = array())
    {
        $this->options = $options + $this->options;

        if (empty($this->options['engine'])) {
            throw new \UnexpectedValueException('Config "engine" not correct');
        }

        $this->start_time = time();
        $this->process = new ChildProcess();
    }

    /**
     * Start to run
     */
    public function run()
    {
        if ($this->_is_run) {
            throw new \RuntimeException("Already running!");
        }

        $this->_is_run = true;
        $this->emit('run');

        $engine = ucfirst($this->options['engine']);

        if (!class_exists($try_engine = __NAMESPACE__ . "\\Adapter\\" . $engine)
            && !class_exists($try_engine = $engine)
        ) {
            throw new \RuntimeException('Unknown adapter engine of "' . $try_engine . '"');
        }


        while (true) {

            $current_time = mktime(date('H'), date('i'), 0);

            $source = new $try_engine($this->options);
            if(ROLE===self::ROLE_BACKEND){
                $spread = 25-date('s');
                if($spread>0){
                    sleep($spread);
                }
            }
            // 如果当前分钟已经在执行了，则本次休息一下，直接进入继续下一次循环
            if($source->checkCurrentMinuteHasRun()){
                $sleep = 60 - date('s');
                sleep($sleep);
                continue;
            }

            // Load tasks
            $source->getNeedsExecTasks();
            $this->emit('getNeedsExecTasks');

            foreach ($source->tasks as $task) {
                $this->dispatch($task);
            }


            $sleep = 60 - (time()-$current_time);

            if($sleep>0){
                sleep($sleep);
            }

            $source = null;
            unset($sleep, $task, $current_time);
        }
    }

    /**
     * Dispatch command
     *
     * @param $command
     */
    protected function dispatch($task)
    {

        $this->emit('execute', $task);
        $that = $this;

        $this->process->parallel(function () use ($task, $that) {
                $status = \Croon\Utils::exec($task->command, $stdout, $stderr);

                $that->emit('executed', $task, array($status, $stdout, $stderr));
            }
        );
    }
} 
