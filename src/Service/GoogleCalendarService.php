<?php
namespace App\Service;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendarService
{
    private Client $client;
    private ?Calendar $calendarService = null;
    private string $calendarId;
    private string $credentialsPath;
    private string $tokenPath;
    private string $clientId;
    private string $clientSecret;

    public function __construct(
        string $credentialsPath, 
        string $calendarId,
        string $clientId,
        string $clientSecret
    ) {
        $this->credentialsPath = $credentialsPath;
        $this->calendarId = $calendarId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenPath = dirname($credentialsPath) . '/google_token.json';
        
        $this->initClient();
    }
    
    private function initClient(): void
    {
        $this->client = new Client();
        $this->client->setApplicationName('Symfony Calendar App');
        $this->client->setScopes(['https://www.googleapis.com/auth/calendar.events']);
        // Set client ID and secret using injected values
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri('http://localhost:8000/oauth2callback');
        $this->client->setAccessType('offline');
        // Set prompt for testing mode
        $this->client->setPrompt('consent');
        
        // Set SSL verification options
        $this->client->setHttpClient(
            new \GuzzleHttp\Client([
                'verify' => true,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false
                ]
            ])
        );
        
        if (file_exists($this->tokenPath)) {
            $accessToken = json_decode(file_get_contents($this->tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }
        
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                try {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $this->saveToken();
                } catch (\Exception $e) {
                    // If refresh fails, we'll need to re-authenticate
                    if (file_exists($this->tokenPath)) {
                        unlink($this->tokenPath);
                    }
                }
            }
        }
    }

    private function saveToken(): void
    {
        if (!file_exists(dirname($this->tokenPath))) {
            mkdir(dirname($this->tokenPath), 0700, true);
        }
        
        file_put_contents($this->tokenPath, json_encode($this->client->getAccessToken()));
    }

    public function isAuthenticated(): bool
    {
        return !empty($this->client->getAccessToken()) && !$this->client->isAccessTokenExpired();
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleAuthCode(string $code): void
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (array_key_exists('error', $accessToken)) {
                throw new \Exception('Error fetching access token: ' . json_encode($accessToken));
            }
            
            $this->client->setAccessToken($accessToken);
            $this->saveToken();
            
        } catch (\Exception $e) {
            throw new \Exception('Authorization failed: ' . $e->getMessage());
        }
    }

    public function addEvent(string $title, string $description, \DateTimeInterface $startDate, string $location): void
    {
        if (!$this->isAuthenticated()) {
            throw new \RuntimeException('Not authenticated with Google Calendar');
        }

        try {
            if (!$this->calendarService) {
                $this->calendarService = new Calendar($this->client);
            }

            $endDate = clone $startDate;
            $endDate->modify('+1 hour');

            $event = new Event([
                'summary' => $title,
                'description' => $description,
                'start' => [
                    'dateTime' => $startDate->format('c'),
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'dateTime' => $endDate->format('c'),
                    'timeZone' => 'Europe/Paris',
                ],
                'location' => $location,
            ]);

            $this->calendarService->events->insert($this->calendarId, $event);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to add event to Google Calendar: ' . $e->getMessage());
        }
    }
}