<?php
declare(strict_types=1);
require_once __DIR__ . '/CnamModel.php';

final class InstallationCNAM extends CnamModel {
    public function migrer(): void {
        $sql = file_get_contents(dirname(__DIR__,2) . '/database/cnam-google.sql');
        $hash = hash('sha256',$sql);
        if (!(int)($this->one("SELECT GET_LOCK('etounsi-cnam-migration',15) AS ok")['ok']??0)) throw new RuntimeException('Une migration est déjà en cours.');
        try {
            $this->execute('CREATE TABLE IF NOT EXISTS cnam_schema_versions (version VARCHAR(40) PRIMARY KEY,sha256 CHAR(64) NOT NULL,applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB');
            $previous = $this->one("SELECT sha256 FROM cnam_schema_versions WHERE version='20261122-cnam-google'");
            if ($previous) {
                if (!hash_equals($previous['sha256'],$hash)) throw new RuntimeException('La migration appliquée a été modifiée. Préparer une nouvelle migration.');
                return;
            }
            $columns = $this->all("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='assure_social'");
            if ($columns && !in_array('matricule_cnam',array_column($columns,'COLUMN_NAME'),true)) {
                $tables = ['document_cnam','decision_cnam','demande_remboursement','beneficiaire','assure_social'];
                $rename = [];
                foreach ($tables as $table) {
                    $exists = $this->one('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?',[$table]);
                    if (!$exists) continue;
                    if ((int)$this->one('SELECT COUNT(*) AS n FROM `' . $table . '`')['n'] > 0)
                        throw new RuntimeException('Données CNAM historiques détectées. Conversion manuelle nécessaire ; aucune donnée ni structure métier n’a été supprimée.');
                    $rename[] = '`' . $table . '` TO `cnam_legacy_' . $table . '`';
                }
                if ($rename) $this->execute('RENAME TABLE ' . implode(',',$rename));
            }
            // DDL MySQL auto-committé : reprise explicite et CREATE IF NOT EXISTS.
            $sql = preg_replace('/^--.*$/m','',$sql);
            foreach (explode(';',$sql) as $statement) if (trim($statement) !== '') $this->execute($statement);
            $this->execute('INSERT INTO cnam_schema_versions(version,sha256) VALUES (?,?)',['20261122-cnam-google',$hash]);
        } finally { $this->one("SELECT RELEASE_LOCK('etounsi-cnam-migration') AS ok"); }
    }
}
