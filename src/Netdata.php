<?php

declare(strict_types=1);

namespace TheRyanHowell\NetdataBundle;

use TheRyanHowell\NetdataBundle\Exceptions\NetdataError;
use TheRyanHowell\NetdataBundle\Exceptions\AuthenticationError;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
class Netdata
{
    private $url;
    private $authenticated;
    private $username;
    private $password;
    private $guzzle;
    private $hostPath;

    public function __construct(
        string $url,
        bool $authenticated = false,
        ?string $username = null,
        ?string $password = null,
        float $timeout = 2.0
    ) {
        $this->url = $url;
        $this->authenticated = $authenticated;
        $this->username = $username;
        $this->password = $password;

        $guzzleOptions = [
            // Base URI is used with relative requests
            'base_uri' => $this->url,
            // You can set any number of default request options.
            'timeout' => $timeout,
        ];

        if (true === $this->authenticated) {
            $guzzleOptions['auth'] = [$this->username, $this->password];
        }

        $this->guzzle = new Client($guzzleOptions);
    }

    public function switchHost(?string $host = null): void
    {
        if(null === $host) {
            $this->hostPath = '';
        } else {
            $this->hostPath = '/host/' . $host;
        }
    }

    public function info(): array
    {
        $response = $this->request('GET', '/api/v1/info');

        return $response;
    }

    public function charts(): array
    {
        $response = $this->request('GET', '/api/v1/charts');

        return $response;
    }

    public function chart(string $chart): array
    {
        $response = $this->request(
            'GET',
            '/api/v1/chart',
            [
                'query' => [
                    'chart' => $chart
                ]
            ]
        );

        return $response;
    }

    public function allmetrics($data = 'average'): array
    {
        $response = $this->request(
            'GET',
            '/api/v1/allmetrics',
            [
                'query' => [
                    'format' => 'json',
                    'data' => $data
                ]
            ]
        );

        return $response;
    }

    public function data(
        string $chart,
        ?array $dimension = null,
        \DateTimeInterface $after,
        ?\DateTimeInterface $before = null,
        int $points = 20,
        string $group = 'average',
        int $gtime = 0,
        array $options = []
    ): array
    {
        $query = [
            'chart' => $chart,
            'after' => $after->getTimestamp(),
            'points' => $points,
            'group' => $group,
            'gtime' => $gtime,
            'options' => $options
        ];

        if(null !== $dimension) {
            $query['dimension'] = $dimension;
        }

        if(null !== $before) {
            $query['before'] = $before->getTimestamp();
        }

        $response = $this->request(
            'GET',
            '/api/v1/data',
            [
                'query' => \GuzzleHttp\Psr7\build_query($query, PHP_QUERY_RFC1738)
            ]
        );

        return $response;
    }

    public function alarms(bool $all): array
    {
        $response = $this->request(
            'GET',
            '/api/v1/alarms',
            [
                'query' => [
                    'all' => $all
                ]
            ]
        );

        return $response;
    }

    public function alarmLog(\DateTimeInterface $after): array
    {
        $response = $this->request(
            'GET',
            '/api/v1/alarm_log',
            [
                'query' => [
                    'after' => $after->getTimestamp(),
                ]
            ]
        );


    }

    private function request($method, $path, $options = [])
    {
        try {
            $response = $this->guzzle->request(
                $method,
                $this->hostPath . $path,
                $options
            );
        } catch(ClientException $e) {
            if($e->getCode() === 401) {
                throw new AuthenticationError();
            } else {
                throw new NetdataError($e->getMessage(), $e->getCode());
            }
        } catch(\Exception $e) {
            throw new NetdataError($e->getMessage(), $e->getCode());
        }

        return json_decode((string) $response->getBody(), true);
    }
}
