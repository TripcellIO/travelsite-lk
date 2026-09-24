-- Apply once to existing installations. Fresh installs receive this table from database.sql.
CREATE TABLE ai_prompt_versions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 prompt_text TEXT NOT NULL,
 created_by BIGINT UNSIGNED NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
