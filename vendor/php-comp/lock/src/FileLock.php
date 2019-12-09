<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午9:53
 */

namespace PhpComp\Lock;

/**
 * Class FileLock
 * - file lock
 * - support *nix and windows
 *
 * @package PhpComp\Lock
 */
class FileLock extends BaseDriver
{
    /**
     * @var resource
     */
    private $fp;

    /**
     * @var int|bool
     */
    private $wouldBlock;

    /**
     * @var array
     */
    protected $options = [
        'single' => false,
        'tmpDir' => '/tmp',
    ];

    protected function init()
    {
        parent::init();

        $this->driver = Lock::DRIVER_FILE;

        $this->options['single'] = (bool)$this->options['single'];
        $this->options['tmpDir'] = trim($this->options['tmpDir']);

        if (!$this->options['tmpDir']) {
            $this->options['tmpDir'] = '/tmp';
        }
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function lock($key, $timeout = self::EXPIRE): bool
    {
        // $startTime = time();
        $file = sprintf('%s/%s.lock', $this->options['tmpDir'], md5(__FILE__ . $key));

        if (!$this->fp = fopen($file, 'wb+')) {
            throw new \RuntimeException('open file failed. FILE: ' . $file);
        }

        // LOCK_SH 取得共享锁定（读取的程序）
        // LOCK_EX 取得独占锁定,排它型锁定（写入的程序）
        // LOCK_UN 释放锁定（无论共享或独占）
        $op = $this->options['single'] ? LOCK_EX + LOCK_NB : LOCK_EX;

        // 如果锁定会堵塞的话（EWOULDBLOCK 错误码情况下），可选的第三个参数会被设置为 TRUE。（Windows 上不支持）
        return flock($this->fp, $op, $this->wouldBlock);
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function unlock($key): bool
    {
        if (!$this->fp) {
            throw new \LogicException('Please use lock() before unlock.');
        }

        // 释放锁定
        $ret = flock($this->fp, LOCK_UN);
        fclose($this->fp);
        $this->fp = null;

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        parent::close();

        if ($this->fp) {
            fclose($this->fp);
            $this->fp = null;
        }
    }

    /**
     * @return bool|int
     */
    public function getWouldBlock()
    {
        return $this->wouldBlock;
    }
}
