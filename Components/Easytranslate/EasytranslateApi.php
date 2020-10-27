<?php

namespace Easytranslate\Components\Easytranslate;

use Exception;

/**
 * Class EasytranslateApi
 * @package Easytranslate\Components\Easytranslate
 */
class EasytranslateApi
{
    const PROD_BASE_URL = 'https://api.platform.easytranslate.com';
    const SANDBOX_BASE_URL = 'https://api.platform.sandbox.easytranslate.com';

    const CURL_OPTIONS = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    );

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $teamIdentifier;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $sandboxMode;

    /**
     * cURL resource
     *
     * @var resource
     */
    protected $curl;

    /**
     * EasytranslateApi constructor.
     * @param $username string
     * @param $password string
     * @param $clientId string
     * @param $clientSecret string
     * @param $sandboxMode bool
     */
    public function __construct(string $username, string $password, string $clientId, string $clientSecret, bool $sandboxMode)
    {
        $this->curl = curl_init();

        $this->username = $username;
        $this->password = $password;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->sandboxMode = $sandboxMode;

        $this->authToken = $this->getAuthToken();
        $this->teamIdentifier = $this->getTeamIdentifier();
    }

    public function __destruct()
    {
        if ($this->curl && is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * @return string
     */
    public function getAuthToken(): string {

        $data = array(
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret,
            "grant_type" => "password",
            "username" => $this->username,
            "password" => $this->password,
            "scope" => "dashboard"
        );

        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/oauth/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));
        $response = curl_exec($this->curl);
        return json_decode($response)->access_token;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUser(): string {
        curl_setopt_array($this->curl, self::CURL_OPTIONS);
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/user",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));

        $response = curl_exec($this->curl);

        if(curl_errno($this->curl)){
            throw new Exception(curl_error($this->curl));
        }

        return $response;
    }

    /**
     * @param $sourceLanguage
     * @param $targetLanguages
     * @param $content
     * @param $callbackUrl
     * @param string $type
     * @param null $folderId
     * @param null $folderName
     * @param null $name
     * @return array
     * @throws Exception
     */
    public function createNewProject($sourceLanguage, $targetLanguages, $content,
                                     $callbackUrl, $name=null, $type='project', $folderId=null,
                                     $folderName=null): array {
        $data = array(
            "data" => array(
                "type" => $type,
                "attributes" => array(
                    "source_language" => $sourceLanguage,
                    "target_languages" => $targetLanguages,
                    "callback_url" => $callbackUrl,
                    "content" => $content,
                    "name" => $name
                ),
                "folder_id" => $folderId,
                "folder_name" => $folderName
            )
        );

        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->teamIdentifier . "/projects",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));
        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new Exception(curl_error($this->curl));
        }

        return json_decode($response, true);
    }

    /**
     * @param $projectId
     * @return array
     * @throws Exception
     */
    public function getProject($projectId): array {
        curl_setopt_array($this->curl, self::CURL_OPTIONS);
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->getTeamIdentifier() . "/projects/" . $projectId,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new \Exception(curl_error($this->curl));
        }

        return json_decode($response, true);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getProjects(): array {
        curl_setopt_array($this->curl, self::CURL_OPTIONS);
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->getTeamIdentifier() . "/projects",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new \Exception(curl_error($this->curl));
        }

        return json_decode($response, true);
    }

    /**
     * @param $projectId
     * @return array
     * @throws Exception
     */
    public function getTasksFromProject($projectId): array {
        curl_setopt_array($this->curl, self::CURL_OPTIONS);
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->getTeamIdentifier() . "/projects/" . $projectId . "/tasks",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new \Exception(curl_error($this->curl));
        }

        return json_decode($response, true );
    }

    /**
     * @param $projectId
     * @param $taskId
     * @return array
     * @throws Exception
     */
    public function getContentForTask($projectId, $taskId): array {
        curl_setopt_array($this->curl, self::CURL_OPTIONS);
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->getTeamIdentifier() . "/projects/" . $projectId . "/tasks/" . $taskId . "/download",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));

        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new \Exception(curl_error($this->curl));
        }

        return json_decode($response, true );
    }

    /**
     * @param $projectId
     * @throws Exception
     */
    public function acceptPriceForProject($projectId) {
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->teamIdentifier . "/projects/" . $projectId . "/accept-price",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));
        curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new \Exception(curl_error($this->curl));
        }
    }

    /**
     * @param $projectId
     * @throws Exception
     */
    public function declinePriceForProject($projectId) {
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->getBaseUrl() . "/api/v1/teams/" . $this->teamIdentifier . "/projects/" . $projectId . "/decline-price",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->authToken
            ),
        ));
        curl_exec($this->curl);

        if (curl_errno($this->curl)){
            throw new \Exception(curl_error($this->curl));
        }
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function getTeamIdentifier() {
        return json_decode($this->getUser())->included[0]->attributes->team_identifier;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string {
        if ($this->sandboxMode) {
            return self::SANDBOX_BASE_URL;
        }
        return self::PROD_BASE_URL;
    }
}
