<?php

namespace App\adms\Controllers\Services;

class FileServer
{
    private array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'zip', 'rar'
    ];
    
    private string $uploadBasePath = 'public/adms/uploads/';

    public function serveFile(string $path): void
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        if (headers_sent($file, $line)) {
            error_log("Headers already sent in $file on line $line");
            exit('Erro: headers já enviados!');
        }

        $base = realpath(__DIR__ . '/../../../../public/adms/uploads');
        $fullPath = realpath($base . DIRECTORY_SEPARATOR . $path);
        file_put_contents(__DIR__ . '/debug_realpath.txt', "BASE: $base\nPATH: $path\nFULL: $fullPath\n", FILE_APPEND);

        // Validar o caminho
        if (empty($path) || strpos($path, '..') !== false) {
            $this->sendError('Caminho inválido', 400);
            return;
        }

        if (!$fullPath || strpos($fullPath, $base) !== 0) {
            $this->sendError('Caminho inválido', 400);
            return;
        }

        if (!file_exists($fullPath)) {
            $this->sendError('Arquivo não encontrado', 404);
            return;
        }

        if (!is_readable($fullPath)) {
            $this->sendError('Arquivo sem permissão de leitura', 403);
            return;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            $this->sendError('Tipo de arquivo não permitido', 403);
            return;
        }

        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed'
        ];

        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        if (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'])) {
            $disposition = ($extension === 'pdf') ? 'inline' : 'attachment';
            header('Content-Disposition: ' . $disposition . '; filename="' . basename($fullPath) . '"');
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));

        // Limpar buffers antes de enviar arquivo binário
        if (ob_get_level()) {
            ob_end_clean();
        }
        flush();

        readfile($fullPath);
        exit;
    }

    private function sendError(string $message, int $code): void
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Erro $code: $message";
        exit;
    }
} 