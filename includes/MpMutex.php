<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class MpMutex
{
    private $file;

    private $own = false;

    public function __construct($key)
    {
        $this->file = fopen(dirname(__FILE__)."/$key.lockfile", 'w+');
    }

    public function __destruct()
    {
        if ($this->own) {
            $this->releaseLock();
        }
    }

    public function lock()
    {
        if (!flock($this->file, LOCK_EX)) {
            return false;
        }
        ftruncate($this->file, 0);
        fwrite($this->file, "Locked\n");
        fflush($this->file);
        $this->own = true;

        return $this->own;
    }

    public function unlock()
    {
        return $this->releaseLock();
    }

    public function releaseLock()
    {
        if ($this->own) {
            if (!flock($this->file, LOCK_UN)) {
                return false;
            }
            ftruncate($this->file, 0);
            fwrite($this->file, "Unlocked\n");
            fflush($this->file);
        }
        $this->own = false;

        return $this->own;
    }
}
