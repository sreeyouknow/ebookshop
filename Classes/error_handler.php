<?php
class ErrorLogger {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleFatalError']);
    }

    private function getErrorTypeName($errno) {
        switch ($errno) {
            case E_ERROR: return "E_ERROR";
            case E_WARNING: return "E_WARNING";
            case E_PARSE: return "E_PARSE";
            case E_NOTICE: return "E_NOTICE";
            case E_CORE_ERROR: return "E_CORE_ERROR";
            case E_CORE_WARNING: return "E_CORE_WARNING";
            case E_COMPILE_ERROR: return "E_COMPILE_ERROR";
            case E_COMPILE_WARNING: return "E_COMPILE_WARNING";
            case E_USER_ERROR: return "E_USER_ERROR";
            case E_USER_WARNING: return "E_USER_WARNING";
            case E_USER_NOTICE: return "E_USER_NOTICE";
            case E_STRICT: return "E_STRICT";
            case E_RECOVERABLE_ERROR: return "E_RECOVERABLE_ERROR";
            case E_DEPRECATED: return "E_DEPRECATED";
            case E_USER_DEPRECATED: return "E_USER_DEPRECATED";
            default: return "UNKNOWN";
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        $error_type = $this->getErrorTypeName($errno);
        $stmt = $this->conn->prepare("INSERT INTO error_logs (error_type, error_message, file, line) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $error_type, $errstr, $errfile, $errline);
        $stmt->execute();

        return true;
    }

    public function handleFatalError() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    public function getLogs() {
        $query = "SELECT * FROM error_logs ORDER BY created_at DESC";
        return $this->conn->query($query);
    }
}
?>
