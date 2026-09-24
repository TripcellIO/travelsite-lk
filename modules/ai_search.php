<?php
declare(strict_types=1);

$defaults = [
    'enabled' => false,
    'provider' => 'openai',
    'model' => '',
    'default_origin' => 'CMB',
    'default_currency' => 'LKR',
    'search_types' => ['flight', 'hotel', 'tour', 'package'],
    'max_destinations' => 5,
    'max_results' => 10,
    'budget_mode' => 'total_trip',
    'recommendations' => true,
    'package_builder' => true,
    'system_prompt' => 'Help travellers plan trips. Extract their dates, origin, travellers, budget and preferences. Treat prices as estimates until verified with a supplier. Never claim an affiliate redirect is a confirmed booking.',
];
$allowedTypes = ['flight', 'hotel', 'tour', 'package'];
$stored = db()->prepare('SELECT `value` FROM settings WHERE `key`=?');
$stored->execute(['ai_search_config']);
$decoded = json_decode((string)($stored->fetchColumn() ?: ''), true);
$config = is_array($decoded) ? array_replace($defaults, $decoded) : $defaults;
$error = '';
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (!in_array($_SESSION['admin_role'] ?? '', ['super_admin', 'admin'], true)) {
        http_response_code(403);
        exit('Administrator access required.');
    }
    $action = $_POST['action'] ?? 'save';
    if ($action === 'restore') {
        $versionId = filter_var($_POST['version_id'] ?? '', FILTER_VALIDATE_INT);
        if (!$versionId || $versionId < 1) {
            $error = 'Select a valid prompt version.';
        } else {
            $query = db()->prepare('SELECT prompt_text FROM ai_prompt_versions WHERE id=?');
            $query->execute([$versionId]);
            $previous = $query->fetchColumn();
            if ($previous === false) $error = 'Prompt version not found.';
            else $config['system_prompt'] = (string)$previous;
        }
    } elseif ($action === 'save') {
        $config = [
            'enabled' => isset($_POST['enabled']),
            'provider' => strtolower(trim((string)($_POST['provider'] ?? ''))),
            'model' => trim((string)($_POST['model'] ?? '')),
            'default_origin' => strtoupper(trim((string)($_POST['default_origin'] ?? ''))),
            'default_currency' => strtoupper(trim((string)($_POST['default_currency'] ?? ''))),
            'search_types' => array_values(array_intersect($allowedTypes, (array)($_POST['search_types'] ?? []))),
            'max_destinations' => (int)($_POST['max_destinations'] ?? 0),
            'max_results' => (int)($_POST['max_results'] ?? 0),
            'budget_mode' => (string)($_POST['budget_mode'] ?? ''),
            'recommendations' => isset($_POST['recommendations']),
            'package_builder' => isset($_POST['package_builder']),
            'system_prompt' => trim((string)($_POST['system_prompt'] ?? '')),
        ];
    } else {
        $error = 'Unknown action.';
    }
    if (!$error) {
        if (!preg_match('/^[a-z][a-z0-9_-]{1,39}$/', $config['provider'])) $error = 'Enter a valid AI provider code.';
        elseif (strlen($config['model']) > 100) $error = 'Model name is too long.';
        elseif (!preg_match('/^[A-Z]{3}$/', $config['default_origin'])) $error = 'Origin must be a three-letter airport code.';
        elseif (!preg_match('/^[A-Z]{3}$/', $config['default_currency'])) $error = 'Currency must be a three-letter code.';
        elseif (!$config['search_types']) $error = 'Select at least one search type.';
        elseif ($config['max_destinations'] < 1 || $config['max_destinations'] > 20) $error = 'Destination limit must be between 1 and 20.';
        elseif ($config['max_results'] < 1 || $config['max_results'] > 50) $error = 'Result limit must be between 1 and 50.';
        elseif (!in_array($config['budget_mode'], ['total_trip', 'per_person'], true)) $error = 'Select a valid budget interpretation.';
        elseif ($config['system_prompt'] === '' || strlen($config['system_prompt']) > 10000) $error = 'Prompt must contain 1 to 10,000 bytes.';
    }
    if (!$error) {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO ai_prompt_versions(prompt_text,created_by) VALUES(?,?)')
                ->execute([$config['system_prompt'], (int)$_SESSION['admin_id']]);
            $pdo->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)')
                ->execute(['ai_search_config', json_encode($config, JSON_THROW_ON_ERROR)]);
            $pdo->commit();
            audit($action === 'restore' ? 'restore_prompt' : 'update', 'ai_search');
            $_SESSION['notice'] = $action === 'restore' ? 'Prompt restored and settings saved.' : 'AI search settings saved.';
            header('Location: ?page=ai-search');
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('AI search configuration save failed: ' . $exception->getMessage());
            $error = 'Could not save AI settings. Check that migration 004 has been applied.';
        }
    }
}
$versions = db()->query('SELECT id,created_at,created_by FROM ai_prompt_versions ORDER BY id DESC LIMIT 10')->fetchAll();
require __DIR__ . '/../views/ai_search.php';
