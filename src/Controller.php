<?php

declare(strict_types=1);

namespace App;

require_once('src/Exception/ConfigurationException.php');

use App\Exception\AppException;
use App\Exception\ConfigurationException;

require_once("View.php");
require_once("Database.php");

class Controller
{
    private const DEFAULT_ACTION = 'list';

    private static array $configuration = [];

    private Database $database;
    private array $request;
    private View $view;

    public static function initConfiguration(array $configuration): void
    {
        self::$configuration = $configuration;
    }

    public function __construct(array $request)
    {
        if (empty(self::$configuration['db'])){
            throw new ConfigurationException('Configuration error');
        }
        $this->database = new Database(self::$configuration['db']);
        $this->request = $request;
        $this->view = new View();
    }

    public function action(): string
    {
        $data = $this->getRequestsGet();
        return $data['action'] ?? self::DEFAULT_ACTION;
    }

    public function run(): void
    {
        $viewParams = [];

        switch ($this->action()) {
            case 'create':
                $page = 'create';
                $created = false;

                $data = $this->getRequestsPost();
                if(!empty($data)){
                    $created = true;

                   $this->database->createNote($data);
                   header('Location: /?before=created');
                }

                $viewParams['created'] = $created;
                break;
            case 'show':
                $viewParams = [
                    'title' => 'Moja notatka',
                    'description' => 'Opis'
                ];
                break;
            default:
                $page = 'list';

                $data = $this->getRequestsGet();

                $notes = $this->database->getNotes();
                $viewParams = [
                    'notes' => $this->database->getNotes(),
                    'before' => $data['before'] ?? null
                ];
                break;
        }
        $this->view->render($page, $viewParams);
    }

    private function getRequestsPost(): array
    {
        return $this->request['post'] ?? [];
    }
    
    private function getRequestsGet(): array
    {
        return $this->request['get'] ?? [];
    }
} 