<?php
declare(strict_types=1);

namespace TheRyanHowell\NetdataBundle;

use TheRyanHowell\NetdataBundle\Netdata;
use PHPUnit\Framework\TestCase;

class NetdataTest extends TestCase
{
    public function setUp(): void
    {
        $this->netdata = new Netdata('http://127.0.0.1:19999');
    }

    public function testInfo()
    {
        $response = $this->netdata->info();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('version', $response);
        $this->assertArrayHasKey('uid', $response);
        $this->assertArrayHasKey('mirrored_hosts', $response);
        $this->assertIsArray($response['mirrored_hosts']);
        $this->assertNotEmpty($response['mirrored_hosts']);
        $this->assertArrayHasKey('alarms', $response);
        $this->assertIsArray($response['alarms']);
        $this->assertNotEmpty($response['alarms']);
    }

    public function testCharts()
    {
        $response = $this->netdata->charts();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('hostname', $response);
        $this->assertArrayHasKey('version', $response);
        $this->assertArrayHasKey('os', $response);
        $this->assertArrayHasKey('timezone', $response);
        $this->assertArrayHasKey('update_every', $response);
        $this->assertArrayHasKey('history', $response);
        $this->assertArrayHasKey('custom_info', $response);
        $this->assertArrayHasKey('charts', $response);
        $this->assertIsArray($response['charts']);
        $this->assertNotEmpty($response['charts']);
        $this->assertArrayHasKey('charts_count', $response);
        $this->assertArrayHasKey('dimensions_count', $response);
        $this->assertArrayHasKey('alarms_count', $response);
        $this->assertArrayHasKey('rrd_memory_bytes', $response);
        $this->assertArrayHasKey('hosts_count', $response);
        $this->assertArrayHasKey('hosts', $response);
        $this->assertIsArray($response['hosts']);
        $this->assertNotEmpty($response['hosts']);
    }

    public function testChart()
    {
        $response = $this->netdata->chart('system.cpu');
        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertArrayHasKey('family', $response);
        $this->assertArrayHasKey('context', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('priority', $response);
        $this->assertArrayHasKey('plugin', $response);
        $this->assertArrayHasKey('module', $response);
        $this->assertArrayHasKey('enabled', $response);
        $this->assertArrayHasKey('units', $response);
        $this->assertArrayHasKey('data_url', $response);
        $this->assertArrayHasKey('chart_type', $response);
        $this->assertArrayHasKey('duration', $response);
        $this->assertArrayHasKey('first_entry', $response);
        $this->assertArrayHasKey('last_entry', $response);
        $this->assertArrayHasKey('update_every', $response);
        $this->assertArrayHasKey('dimensions', $response);
        $this->assertIsArray($response['dimensions']);
        $this->assertNotEmpty($response['dimensions']);
        $this->assertArrayHasKey('green', $response);
        $this->assertArrayHasKey('red', $response);
        $this->assertArrayHasKey('alarms', $response);
        $this->assertIsArray($response['alarms']);
        $this->assertNotEmpty($response['alarms']);
    }

    public function testAllMetrics()
    {
        $response = $this->netdata->allmetrics();
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testData()
    {
        $after = new \DateTime();
        $after->modify('-1 hour');
        $response = $this->netdata->data(
            'system.cpu',
            ['guest', 'steal'],
            $after,
            null,
            25,
            'average',
            5,
            ['nonzero']
        );

        $this->assertIsArray($response);
        $this->assertArrayHasKey('labels', $response);
        $this->assertIsArray($response['labels']);
        $this->assertNotEmpty($response['labels']);
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);
        $this->assertNotEmpty($response['data']);
    }

    public function testAlarms()
    {
        $response = $this->netdata->alarms(false);
        $this->assertArrayHasKey('hostname', $response);
        $this->assertArrayHasKey('latest_alarm_log_unique_id', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('now', $response);
        $this->assertArrayHasKey('alarms', $response);
        $this->assertIsArray($response['alarms']);
    }

    public function testHostSwitch()
    {
        $info = $this->netdata->info();
        $host = $info['mirrored_hosts'][0];
        $this->netdata->switchHost($host);
        $info = $this->netdata->info();
        $this->assertArrayHasKey('collectors', $info);
        $this->netdata->switchHost();
        $info = $this->netdata->info();
        $this->assertArrayHasKey('collectors', $info);
    }
}
