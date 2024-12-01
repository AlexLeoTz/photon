<?php
require_once __DIR__ . '/../src/SimpleWire.php';
require_once __DIR__ . '/../src/Database.php';

use SimpleWire\SimpleWire;
use SimpleWire\Database;

header('Content-Type: text/html');

try {
    $wire = new SimpleWire();

    $rawInput = file_get_contents('php://input');
    if ($rawInput === false) {
        throw new RuntimeException('Failed to read input');
    }

    $data = json_decode($rawInput, true, 512, JSON_THROW_ON_ERROR);

    if (!isset($data['action'])) {
        throw new RuntimeException('Missing required action field');
    }

    // Define allowed actions
    $allowedActions = ['increment', 'decrement', 'count', 'submit-contact', 'navigate'];
    if (!in_array($data['action'], $allowedActions, true)) {
        throw new RuntimeException('Invalid action');
    }

    // Reset previous form states
    $wire->setState('form_success', false);
    $wire->setState('form_error', '');

    switch ($data['action']) {
        case 'increment':
            $currentCount = $wire->getState('count') ?? 0;
            $wire->setState('count', (int)$currentCount + 1);
            break;

        case 'decrement':
            $currentCount = $wire->getState('count') ?? 0;
            $wire->setState('count', max(0, (int)$currentCount - 1));
            break;

        case 'count':
            if (!isset($data['data']['value'])) {
                throw new RuntimeException('Missing value for count action');
            }
            $newValue = filter_var($data['data']['value'], FILTER_VALIDATE_INT);
            if ($newValue === false) {
                throw new RuntimeException('Invalid count value');
            }
            $wire->setState('count', max(0, $newValue));
            break;

        case 'submit-contact':
            if (!isset($data['data']['name'], $data['data']['email'], $data['data']['message'])) {
                throw new RuntimeException('Missing required fields');
            }

            $name = trim($data['data']['name']);
            $email = filter_var(trim($data['data']['email']), FILTER_VALIDATE_EMAIL);
            $message = trim($data['data']['message']);

            if (empty($name) || strlen($name) > 100) {
                throw new RuntimeException('Invalid name');
            }

            if (!$email) {
                throw new RuntimeException('Invalid email address');
            }

            if (empty($message) || strlen($message) > 1000) {
                throw new RuntimeException('Invalid message');
            }

            $db = new Database();
            $db->saveMessage($name, $email, $message);
            $wire->setState('form_success', true);
            break;

        case 'navigate':
            if (!isset($data['data']['component'])) {
                throw new RuntimeException('Missing component for navigation');
            }
            echo $wire->render($data['data']['component']);
            return;
    }

    // Return the updated component
    echo $wire->render($data['action'] === 'submit-contact' ? 'contact-form' : 'counter');

} catch (RuntimeException $e) {
    http_response_code(400);
    $wire->setState('form_error', $e->getMessage());
    echo $wire->render('contact-form');
} catch (Exception $e) {
    http_response_code(500);
    $wire->setState('form_error', 'Internal server error');
    echo $wire->render('contact-form');
}