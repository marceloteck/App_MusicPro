<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deploy do Projeto</title>
</head>
<body>
    <h1>Deploy do Projeto</h1>

    <!-- Formulário para acionar o deploy -->
    <form method="POST" action="">
        <button type="submit" name="deploy">Realizar Deploy Manual</button>
    </form>
    <br>
    <br>

<?php

// Obtém o payload enviado pelo GitHub
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Verifica se o evento é um "push"
if ($data['ref'] === 'refs/heads/app_musicPro') { // Certifique-se de que este é o branch correto
    // Caminho do seu projeto na Hostinger
    //$repo_path = '/home/u234567638/public_html/tocaremusic';
    $repo_path = 'public_html/tocaremusic';

    // Comandos de atualização do repositório
    $output = shell_exec("cd {$repo_path} && git pull origin app_musicPro");
    //cd public_html/tocaremusic && git pull origin app_musicPro

    // Salva o log (opcional)
    file_put_contents($repo_path . "public/deploy.log", date('Y-m-d H:i:s') . "\n" . $output . "\n", FILE_APPEND);

    echo "Deploy realizado!";
} else {
    echo "[ Nenhum deploy Automático necessário. ] <br><br>";
}

function executarDeploy() {
    try {
        // Caminho do repositório
        $repo_path = 'public_html/tocaremusic';
        
        // Comando git para puxar as atualizações
        $output = shell_exec("cd {$repo_path} && git pull origin app_musicPro");
    
        // Verifique se o comando foi bem-sucedido
        if ($output === null) {
            echo "Erro ao executar o comando git pull. <br>";
        }
    
        // Caminho completo para o arquivo de log
        $log_file = $repo_path . "/public/deploy.log";
    
        // Verifica se o diretório existe e tem permissões de escrita
        if (!is_dir($repo_path . "/public")) {
            mkdir($repo_path . "/public", 0777, true);  // Cria o diretório, se necessário
        }
    
        if (!is_writable($log_file)) {
            throw new Exception("Erro: O arquivo de log não tem permissões de escrita.");
        }
    
        // Grava o log com a data e o output do comando git
        file_put_contents($log_file, date('Y-m-d H:i:s') . "\n" . $output . "\n", FILE_APPEND);
    
        // Mensagem de sucesso
        echo "Deploy manual realizado!";
        
    } catch (Exception $e) {
        // Em caso de erro, grava a mensagem no log
        $log_file = $repo_path . "/public/deploy.log";
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Erro: " . $e->getMessage() . "\n", FILE_APPEND);
    
        // Exibe a mensagem de erro para depuração
        echo "Erro durante o deploy: " . $e->getMessage();
    }
}

// Verifica se o botão de deploy foi pressionado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deploy'])) {
    executarDeploy();
}

?>


</body>
</html>