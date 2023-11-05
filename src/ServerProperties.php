<?php

namespace Lupecat;

class ServerProperties {

    protected $serverLoad;
    protected $memoryUsage;
    protected $memoryPeakUsage;
    protected $diskTotalSpace;
    protected $diskFreeSpace;
    protected $cpuUsage;

    public function __construct() {

        // Obtener la carga del servidor
        $this->serverLoad = sys_getloadavg();

        // Obtener el uso de memoria
        $this->memoryUsage = memory_get_usage();
        $this->memoryPeakUsage = memory_get_peak_usage();

        // Obtener el espacio en disco
        $this->diskTotalSpace = disk_total_space('/');
        $this->diskFreeSpace = disk_free_space('/');

        // Obtener el estado del procesador (uso de CPU)
        $this->cpuUsage = sys_getloadavg()[0];

    }

    /**
     * @return array|false
     */
    public function getServerLoad()
    {
        return $this->serverLoad;
    }

    /**
     * @return int
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * @return int
     */
    public function getMemoryPeakUsage()
    {
        return $this->memoryPeakUsage;
    }

    /**
     * @return false|float
     */
    public function getDiskTotalSpace()
    {
        return $this->diskTotalSpace;
    }

    /**
     * @return false|float
     */
    public function getDiskFreeSpace()
    {
        return $this->diskFreeSpace;
    }

    /**
     * @return mixed
     */
    public function getCpuUsage()
    {
        return $this->cpuUsage;
    }

}