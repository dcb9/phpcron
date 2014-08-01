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
use Croon\Utils;

class Phpcron extends EventEmitter{

   protected $_is_run = false;
    protected $options = array(
        'engine'=>'pdo',

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
                $status = Utils::exec($task->command, $stdout, $stderr);

                $that->emit('executed', $task, array($status, $stdout, $stderr));
            }
        );
    }
} 
