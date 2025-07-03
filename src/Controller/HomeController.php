<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private Connection $connection,
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Check all services
        $dbStatus = $this->checkDatabaseConnection();
        $redisStatus = $this->checkRedisConnection();
        $mailhogStatus = $this->checkMailhogConnection();
        $encoreStatus = $this->checkEncoreAssets();
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'welcome_message' => 'Docker Container Health Dashboard',
            'service_checks' => [
                'database' => $dbStatus,
                'redis' => $redisStatus,
                'mailhog' => $mailhogStatus,
                'encore' => $encoreStatus
            ]
        ]);
    }

    private function checkDatabaseConnection(): array
    {
        try {
            // Try to execute a simple query
            $this->connection->executeQuery('SELECT 1');
            
            // Get database info
            $platform = $this->connection->getDatabasePlatform()->getName();
            
            // Get server version through a query
            $versionResult = $this->connection->executeQuery('SELECT version()');
            $version = $versionResult->fetchOne();
            
            return [
                'name' => 'MySQL DB',
                'connected' => true,
                'message' => 'Database container connected',
                'details' => ucfirst($platform) . ' service ready',
                'version' => $version ? substr($version, 0, 50) . '...' : 'Unknown',
                'icon' => '✅',
                'color' => 'green'
            ];
        } catch (\Exception $e) {
            return [
                'name' => 'MySQL DB',
                'connected' => false,
                'message' => 'Database container unavailable',
                'error' => $e->getMessage(),
                'version' => 'Unknown',
                'icon' => '❌',
                'color' => 'red'
            ];
        }
    }

    private function checkRedisConnection(): array
    {
        try {
            // Check Redis container with socket connection
            $socket = @fsockopen('redis', 6379, $errno, $errstr, 3);
            if ($socket) {
                // Send PING command
                fwrite($socket, "*1\r\n$4\r\nPING\r\n");
                $response = fread($socket, 1024);
                fclose($socket);
                
                if (strpos($response, '+PONG') !== false) {
                    return [
                        'name' => 'Redis Cache',
                        'connected' => true,
                        'message' => 'Redis container responding',
                        'details' => 'Cache service ready',
                        'icon' => '✅',
                        'color' => 'green'
                    ];
                }
            }
            
            return [
                'name' => 'Redis Cache',
                'connected' => false,
                'message' => 'Redis container unavailable',
                'details' => 'Service not responding on port 6379',
                'icon' => '❌',
                'color' => 'red'
            ];
        } catch (\Exception $e) {
            return [
                'name' => 'Redis Cache',
                'connected' => false,
                'message' => 'Redis container check failed',
                'error' => $e->getMessage(),
                'icon' => '❌',
                'color' => 'red'
            ];
        }
    }

    private function checkMailhogConnection(): array
    {
        try {
            // Check if Mailhog API is accessible
            $response = $this->httpClient->request('GET', 'http://mailhog:8025/api/v1/messages?limit=1', [
                'timeout' => 3
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                $messageCount = is_array($data) ? count($data) : 0;
                
                return [
                    'name' => 'Mailhog SMTP',
                    'connected' => true,
                    'message' => 'Mailhog container responding',
                    'details' => "Mail testing ready ({$messageCount} messages)",
                    'icon' => '✅',
                    'color' => 'green'
                ];
            } else {
                return [
                    'name' => 'Mailhog SMTP',
                    'connected' => false,
                    'message' => 'Mailhog API error',
                    'details' => 'Status: ' . $response->getStatusCode(),
                    'icon' => '❌',
                    'color' => 'red'
                ];
            }
        } catch (\Exception $e) {
            return [
                'name' => 'Mailhog SMTP',
                'connected' => false,
                'message' => 'Mailhog container unavailable',
                'error' => $e->getMessage(),
                'icon' => '❌',
                'color' => 'red'
            ];
        }
    }

    private function checkEncoreAssets(): array
    {
        try {
            $publicDir = $this->getParameter('kernel.project_dir') . '/public';
            
            // Check if Encore manifest exists
            $manifestPath = $publicDir . '/build/manifest.json';
            $entrypointsPath = $publicDir . '/build/entrypoints.json';
            
            $manifestExists = file_exists($manifestPath);
            $entrypointsExists = file_exists($entrypointsPath);
            
            if ($manifestExists && $entrypointsExists) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $entrypoints = json_decode(file_get_contents($entrypointsPath), true);
                
                $assetCount = count($manifest ?? []);
                $entrypointCount = count($entrypoints['entrypoints'] ?? []);
                
                return [
                    'name' => 'Webpack Encore',
                    'connected' => true,
                    'message' => 'Encore assets compiled',
                    'details' => "{$assetCount} assets, {$entrypointCount} entrypoints",
                    'icon' => '✅',
                    'color' => 'green'
                ];
            } else {
                return [
                    'name' => 'Webpack Encore',
                    'connected' => false,
                    'message' => 'Encore assets missing',
                    'details' => 'Run: npm run build or npm run dev',
                    'icon' => '⚠️',
                    'color' => 'yellow'
                ];
            }
        } catch (\Exception $e) {
            return [
                'name' => 'Webpack Encore',
                'connected' => false,
                'message' => 'Encore check failed',
                'error' => $e->getMessage(),
                'icon' => '❌',
                'color' => 'red'
            ];
        }
    }
}
