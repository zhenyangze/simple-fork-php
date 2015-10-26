<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/10/26
 * Time: 15:06
 */
class SemaphoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Jenner\SimpleFork\Lock\Semaphore
     */
    protected $lock;

    public function setUp(){
        $this->lock = \Jenner\SimpleFork\Lock\Semaphore::create("test");
    }

    public function tearDown(){
        unset($this->lock);
    }

    public function testLock(){
        $this->assertTrue($this->lock->acquire());
        $this->assertTrue($this->lock->release());
    }

    public function testAcquireException(){
        $this->setExpectedException("RuntimeException");
        $this->lock->acquire();
        $this->lock->acquire();
    }

    public function testReleaseException(){
        $this->setExpectedException("RuntimeException");
        $this->lock->release();
    }

    public function testCommunication(){
        $process = new \Jenner\SimpleFork\Process(function(){
            $lock = \Jenner\SimpleFork\Lock\Semaphore::create('test');
            $lock->acquire();
            sleep(5);
            $lock->release();
        });
        $process->start();
        sleep(3);
        $lock = \Jenner\SimpleFork\Lock\Semaphore::create("test");
        $this->assertFalse($lock->acquire());
        $process->wait();
        $this->assertTrue($lock->acquire());
        $this->assertTrue($lock->release());
    }
}